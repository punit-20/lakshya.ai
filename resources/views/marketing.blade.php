@extends('layouts.admin')

@php
    $role = auth()->user()->role;
    $isImpersonating = session()->has('impersonating_client_id');
    $isClient = ($role === 'client' || $isImpersonating);
    
    $prefix = $isClient ? 'client' : 'admin';
    $title = $isClient ? 'AI Creative Builder' : 'AI Marketer';
    $description = 'Generate high-converting digital marketing campaigns in seconds with Gemini 2.5 Flash, review outputs, and publish instantly.';
    
    $user = $isImpersonating ? \App\Models\User::find(session('impersonating_client_id')) : auth()->user();
    $avatarName = $isClient ? $user->name : 'Lakshya Marketer';
    $avatarLetter = $isClient ? substr($user->name, 0, 1) : 'L';
@endphp

@section('title', $title)

@include('partials.marketing-builder', [
    'title' => $title,
    'description' => $description,
    'generateSocialRoute' => route($prefix . '.marketing.generate-social'),
    'generateGrowthRoute' => route($prefix . '.marketing.generate-growth'),
    'generateCampaignRoute' => route($prefix . '.marketing.generate-campaign'),
    'launchRoute' => route($prefix . '.marketing.launch'),
    'avatarName' => $avatarName,
    'avatarLetter' => $avatarLetter
])
