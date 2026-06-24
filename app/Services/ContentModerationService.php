<?php

namespace App\Services;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\Notification;

class ContentModerationService
{
    /**
     * The NSFW detection patterns organized by category.
     */
    protected array $patterns;

    public function __construct()
    {
        $this->patterns = $this->getNsfwPatterns();
    }

    /**
     * Check if text contains NSFW or prohibited advertising content.
     */
    public function isNsfwContent(?string $text): bool
    {
        if (empty($text) || !is_string($text)) {
            return false;
        }

        $text = preg_replace('/\s+/', ' ', trim($text));

        // Leetspeak & Evasion Normalization
        $leetMap = [
            '@' => 'a', '4' => 'a', '^' => 'a',
            '8' => 'b',
            '(' => 'c', '<' => 'c', '{' => 'c',
            '3' => 'e', '€' => 'e',
            '6' => 'g', '9' => 'g',
            '#' => 'h',
            '1' => 'i', '!' => 'i', '|' => 'i',
            '0' => 'o',
            '5' => 's', '$' => 's',
            '7' => 't', '+' => 't',
            'µ' => 'u',
            '¥' => 'y',
            '2' => 'z',
        ];
        $normalizedText = strtolower($text);
        $normalizedText = preg_replace('/(?<=[a-z0-9])[\.\-\_\*\~]+(?=[a-z0-9])/i', '', $normalizedText);
        $normalizedText = strtr($normalizedText, $leetMap);

        $textsToCheck = array_unique([$text, $normalizedText]);

        foreach ($textsToCheck as $checkText) {
            foreach ($this->patterns as $pattern) {
                if (preg_match($pattern, $checkText)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check request inputs for NSFW content and enforce penalties.
     *
     * @param array $inputs
     * @param int $userId
     * @param string $action
     * @param string $ipAddress
     * @return array|null Returns error response array or null
     */
    public function checkInputs(array $inputs, int $userId, string $action, string $ipAddress = ''): ?array
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        // Skip NSFW checks for admin users
        if ($user->role === 'admin') {
            return null;
        }

        $creditService = app(CreditService::class);

        foreach ($inputs as $field => $value) {
            if (!is_string($value)) {
                continue;
            }

            if ($this->isNsfwContent($value)) {
                $penaltyCredits = config('pricing.nsfw_penalty_credits', 5);
                $creditService->deductCredits(
                    $userId,
                    $penaltyCredits,
                    'NSFW Content Blocked',
                    "Attempted to submit NSFW/Prohibited content in field '{$field}': '{$value}'"
                );

                AuditLog::create([
                    'user_id' => $userId,
                    'action' => "NSFW Security Block: Prohibited content in '{$field}' ({$penaltyCredits} credits penalty)",
                    'target_table' => 'credit_usage_logs',
                    'ip_address' => $ipAddress,
                ]);

                Notification::create([
                    'user_id' => $userId,
                    'title' => 'Security Alert: NSFW Penalty! ⚠️',
                    'message' => "Prohibited advertising content was detected in '{$field}'. A penalty of {$penaltyCredits} credits has been deducted.",
                    'is_read' => false,
                ]);

                Notification::create([
                    'user_id' => config('admin.user_id', 1),
                    'title' => 'Client NSFW Violation! ⚠️',
                    'message' => "Client '{$user->name}' (ID: {$userId}) triggered NSFW check in '{$field}'. {$penaltyCredits} credits penalty deducted.",
                    'is_read' => false,
                ]);

                return [
                    'success' => false,
                    'nsfw' => true,
                    'error' => "Security Alert: NSFW/Prohibited content detected (sexually explicit, harassment, porn, etc.). A penalty of {$penaltyCredits} credits has been deducted from your subscription balance.",
                ];
            }
        }

        return null;
    }

    /**
     * Get the NSFW detection patterns.
     */
    protected function getNsfwPatterns(): array
    {
        return [
            // 1. Vulgar Anatomy, Body Parts & Profanity
            '/\b(vagina|vaginal|penis|penile|dick|dicks|dickhead|dicksucker'
            . '|cock|cocks|cocksucker|cocksucking|cockhead|cock\s?ring'
            . '|pussy|pussies|pussy\s?licking|cunt|cunts|cunnilingus'
            . '|clit|clitoris|tit|tits|titties|titty|boob|boobs|boobies'
            . '|ass|asshole|arsehole|arse|assmunch|ass\s?lick'
            . '|anus|anal|rectum|ballsack|ball\s?sack|balls|nutsack|nut\s?sack'
            . '|testicle|testicles|scrotum|pubic|pubes|vulva|labia'
            . '|schlong|dong|johnson|pecker|wiener|willy|knob|bellend'
            . '|boner|erection|hard[\s\-]?on|stiffy|woody'
            . '|cum|cumming|cumshot|cumshots|jizz|spunk|splooge|spooge|semen'
            . '|ejaculat(e|ion|ing)|squirt(ing|ed)?|cream\s?pie|creampie'
            . '|fuck|fucking|fucked|fucker|fuckers|fuckin|fucks|fuckhead'
            . '|motherfucker|motherfucking|clusterfuck|fuckface|fuckwit'
            . '|shit|shitty|shithead|shitting|bullshit|dipshit|horseshit'
            . '|bitch|bitches|bitchy|son\s?of\s?a\s?bitch'
            . '|bastard|bastards|whore|whoring|slut|slutty|sluts|skank|skanky'
            . '|ho|hoe|tramp|wank|wanker|wanking|tosser|twat|twats'
            . '|damn|goddamn|crap|piss|pissing|pissed|piss\s?off'
            . '|horny|kinky|kink|smut|smutty|filthy|raunchy|lewd|lewdness'
            . '|sperm|precum|pre[\s\-]?cum|wet\s?dream|nocturnal\s?emission'
            . '|camel\s?toe|moose\s?knuckle|nip\s?slip|nipple|nipples'
            . '|jack\s?off|jerk\s?off|beat\s?off|rub\s?one\s?out'
            . '|masturbat(e|ion|ing)|fap|fapping|wanking'
            . '|queef|quim|minge|snatch|gash|muff|beaver'
            . '|butt|butthole|buttcheeks|butt\s?plug|buttplug)\b/i',

            // 2. Sexually Explicit Content & Pornography
            '/\b(porn|pornography|pornographic|porno|sex\s?tape|xxx|nsfw|erotic|erotica'
            . '|eroticism|nude|nudity|nudes|naked|hentai|stripclub|strip\s?club|stripper'
            . '|striptease|escort\s?service|escorts?\s?girls?|prostitut(e|ion)|call\s?girl'
            . '|hooker|brothel|cam\s?girl|cam\s?sex|webcam\s?sex|camslut|camwhore'
            . '|adult\s?content|adult\s?film|adult\s?video|adult\s?entertainment'
            . '|adult\s?dating|adult\s?chat|livesex|live\s?sex'
            . '|blowjob|blow\s?job|hand\s?job|handjob|footjob|rimjob'
            . '|intercourse|orgasm|orgy|group\s?sex|gay\s?sex|lesbian\s?sex'
            . '|fetish|foot\s?fetish|bondage|bdsm|dominatrix|domination|dommes|femdom'
            . '|dildo|vibrator|sex\s?toy|sex\s?toys|fleshlight|strap\s?on|strapon'
            . '|butt\s?plug|ball\s?gag|leather\s?restraint'
            . '|anal\s?sex|oral\s?sex|threesome|gangbang|gang\s?bang|double\s?penetration'
            . '|milf|gilf|sexual\s?services|sexual\s?favou?rs'
            . '|lap\s?dance|peep\s?show|phone\s?sex|sexting|sexcam|sex\s?cam'
            . '|sugar\s?daddy|sugar\s?baby|sugar\s?dating|swinger|cuckold'
            . '|deepfake\s?porn|revenge\s?porn|upskirt|voyeur|voyeurweb'
            . '|bukkake|gokkun|golden\s?shower|brown\s?shower'
            . '|futanari|rule\s?34|ahegao|ecchi|yaoi|yuri|harem'
            . '|incest|bestiality|beastiality|zoophilia|necrophilia'
            . '|sensual\s?massage|only\s?fans|chaturbate|xvideos|xhamster|pornhub'
            . '|redtube|brazzers|bangbros|naughty\s?america|playboy|playgirl'
            . '|booty\s?call|hookup\s?site|one\s?night\s?stand|casual\s?sex'
            . '|barely\s?legal|girls\s?gone\s?wild|shemale|she[\s\-]?male|ladyboy|tranny'
            . '|bareback|doggy\s?style|doggystyle|doggie\s?style|missionary\s?position'
            . '|reverse\s?cowgirl|scissoring|fisting|fingering|fingerbang'
            . '|deep\s?throat|deepthroat|throating|gagging'
            . '|lemon\s?party|tub\s?girl|tubgirl|goatse|blue\s?waffle'
            . '|felch|feltch|frotting|pegging|figging|shibari|kinbaku'
            . '|ponyplay|urethra\s?play|urophilia|coprolagnia|coprophilia'
            . '|sodomize|sodomy|rimming|anilingus|tea\s?bagging'
            . '|dirty\s?sanchez|cleveland\s?steamer|hot\s?carl|rusty\s?trombone'
            . '|alaskan\s?pipeline|alabama\s?hot\s?pocket|blumpkin|donkey\s?punch'
            . '|nympho|nymphomania|sadomasochism|sadism|viagra|cialis'
            . '|make\s?me\s?come|spread\s?legs|undressing|topless)\b/i',

            // 3. Violence, Threats, Harassment & Hate Speech
            '/\b(murder(ing|er|ed)?|assassinat(e|ion)|massacr(e|ing)|genocide'
            . '|ethnic\s?cleansing|slaughter(ing|ed)?|beheading|behead'
            . '|decapitat(e|ion)|dismember(ment)?|mutilat(e|ion)|tortur(e|ing|ed)'
            . '|rape|rapist|raping|gang\s?rape|sexual\s?assault|molest(ation|er|ing)?'
            . '|child\s?abuse|domestic\s?violence|harass(ment|ing|er)?|stalk(er|ing)'
            . '|death\s?threat|threat(en)?\s?to\s?kill|i\s?will\s?kill|mass\s?shooting'
            . '|school\s?shooting|serial\s?killer|hate\s?crime|hate\s?speech'
            . '|white\s?supremac(y|ist)|white\s?power|neo[\s\-]?nazi|neonazi|swastika'
            . '|ku\s?klux|kkk|lynch(ing)?'
            . '|racial\s?slur|nigger|nigga|faggot|fag|tranny|kike'
            . '|spic|chink|wetback|gook|raghead|towelhead|beaner|beaners'
            . '|coon|coons|darkie|honkey|pikey|paki|slanteye|jigaboo|jiggaboo'
            . '|retard(ed)?|spastic|bully(ing)?|cyberbully(ing)?'
            . '|doxxing|dox(ed)?|swatting|bloodbath|carnage|war\s?crime'
            . '|ethnic\s?hatred|xenophob(ia|ic)|homophob(ia|ic)|islamophob(ia|ic)'
            . '|antisemit(ic|ism)|misogyn(y|ist)|violent\s?extremis(m|t)'
            . '|terrorist|terrorism|terror\s?attack|jihad(ist)?|radicali[sz](ation|ed))\b/i',

            // 4. Self-Harm & Dangerous Content
            '/\b(suicide|suicidal|kill\s?myself|kill\s?yourself|end\s?my\s?life'
            . '|self[\s\-]?harm|self[\s\-]?mutilat(e|ion)|cut(ting)?\s?myself'
            . '|slit\s?my\s?wrist|hang\s?myself|overdos(e|ing)|pro[\s\-]?ana'
            . '|pro[\s\-]?mia|anorexia\s?tips|bulimia\s?tips|thinspo|thinspiration'
            . '|how\s?to\s?die|painless\s?death|suicide\s?method'
            . '|jump\s?off\s?bridge|poison\s?myself)\b/i',

            // 5. Illegal Drugs & Controlled Substances
            '/\b(cocaine|crack\s?cocaine|heroin|methamphetamine|meth\s?lab'
            . '|crystal\s?meth|fentanyl|opioid\s?abuse|opium'
            . '|mdma|ecstasy|molly\s?drug|lsd|acid\s?trip|psilocybin'
            . '|magic\s?mushrooms|shrooms|ketamine|ghb|date\s?rape\s?drug|rohypnol'
            . '|pcp|angel\s?dust|dmt|ayahuasca|bath\s?salts'
            . '|spice\s?drug|k2\s?drug|buy\s?drugs|sell\s?drugs|drug\s?dealer'
            . '|drug\s?cartel|narcotics|drug\s?trafficking|smuggl(e|ing)'
            . '|buy\s?cocaine|buy\s?heroin|buy\s?meth|darknet\s?drugs'
            . '|adderall\s?without|xanax\s?without|oxycontin'
            . '|oxycodone|hydrocodone|codeine\s?syrup|lean\s?drug|purple\s?drank'
            . '|poppers|khat|mescaline|salvia|kratom\s?high|nitrous\s?oxide\s?high'
            . '|huffing|inhalant\s?abuse)\b/i',

            // 6. Weapons, Firearms & Explosives
            '/\b(buy\s?guns?|sell\s?guns?|illegal\s?guns?|ghost\s?gun|unregistered\s?gun'
            . '|pistol|handgun|rifle|shotgun|assault\s?rifle|automatic\s?weapon'
            . '|machine\s?gun|ammunition|ammo|bullets'
            . '|firearms?\s?sale|weapons?\s?sale|buy\s?firearms?|buy\s?weapons?'
            . '|grenade|explosives?|c4\s?explosive|pipe\s?bomb|bomb\s?making'
            . '|improvised\s?explosive|detonator|silencer|suppressor'
            . '|3d[\s\-]?printed\s?gun|switchblade|brass\s?knuckles'
            . '|ar[\s\-]?15|ak[\s\-]?47|uzi|glock|desert\s?eagle'
            . '|hollow\s?point|armor[\s\-]?piercing|extended\s?magazine'
            . '|high\s?capacity\s?magazine)\b/i',

            // 7. Scams, Fraud, Hacking & Deceptive Practices
            '/\b(carding|credit\s?card\s?fraud|identity\s?theft|phishing'
            . '|scam(mer|ming)?'
            . '|hack(ing|er|ed)?\s?(account|email|password|bank)'
            . '|ddos|botnet|malware|ransomware|keylogger|spyware'
            . '|trojan|zero[\s\-]?day|sql\s?injection|money\s?laundering'
            . '|ponzi\s?scheme|pyramid\s?scheme|get\s?rich\s?quick'
            . '|wire\s?fraud|bank\s?fraud|counterfeit|fake\s?id'
            . '|click\s?fraud|ad\s?fraud|bot\s?traffic|fake\s?followers'
            . '|buy\s?followers|buy\s?likes|buy\s?views'
            . '|crypto\s?scam|bitcoin\s?scam|rug\s?pull|pump\s?and\s?dump'
            . '|dark\s?web|darknet\s?market|black\s?market'
            . '|stolen\s?credit\s?card|stolen\s?data|data\s?breach\s?sale'
            . '|sim\s?swap|account\s?takeover|pharming'
            . '|romance\s?scam|catfish(ing)?|lottery\s?scam'
            . '|mlm\s?scam|forex\s?scam|binary\s?options?\s?scam)\b/i',

            // 8. Child Exploitation & CSAM (Zero Tolerance)
            '/\b(child\s?porn(ography)?|csam|pthc|minor\s?sex|underage\s?sex'
            . '|underage\s?nude|underage\s?dating|pedophil(e|ia|ic)'
            . '|paedophil(e|ia|ic)|pedobear|child\s?exploit(ation)?|child\s?traffick(ing)?'
            . '|child\s?abuse\s?material|grooming\s?minor|lolicon|shotacon'
            . '|loli|shota|lolita|jailbait|jail\s?bait|underage\s?girl|underage\s?boy'
            . '|minor\s?explicit|teen\s?porn|preteen|pre[\s\-]?teen\s?nude'
            . '|nambla)\b/i',

            // 9. Human Trafficking & Modern Slavery
            '/\b(human\s?traffick(ing|er)?|sex\s?traffick(ing|er)?|forced\s?labou?r'
            . '|modern\s?slavery|slave\s?trade|organ\s?harvesting'
            . '|organ\s?traffick(ing)?|mail[\s\-]?order\s?bride|buy\s?a\s?wife'
            . '|forced\s?prostitution|sex\s?slave|indentured\s?servitude'
            . '|labor\s?exploitation|sweatshop)\b/i',

            // 10. Dangerous Misinformation & Banned Health Claims
            '/\b(fake\s?cure|miracle\s?cure|covid\s?cure|cancer\s?cure\s?secret'
            . '|anti[\s\-]?vax\s?proof|vaccine\s?kills?|5g\s?corona'
            . '|microchip\s?vaccine|flat\s?earth\s?proof|crisis\s?actor'
            . '|false\s?flag\s?attack|deep\s?state\s?control|qanon|adrenochrome'
            . '|chemtrail|government\s?mind\s?control'
            . '|fda\s?banned\s?cure|big\s?pharma\s?hiding'
            . '|detox\s?miracle|magic\s?pill|instant\s?cure)\b/i',
        ];
    }
}