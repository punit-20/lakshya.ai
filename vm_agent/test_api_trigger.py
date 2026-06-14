import sys
import os
import traceback

sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from api import app

print("Testing Flask app trigger handler directly...")
with app.test_request_context(method='POST'):
    try:
        # Get the trigger function from view functions
        trigger_fn = app.view_functions['trigger']
        response = trigger_fn()
        print(f"Response data: {response.get_data(as_text=True)}")
    except Exception as e:
        print("Exception raised:")
        traceback.print_exc()
