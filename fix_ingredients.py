import requests
import json

# Configuration
API_URL = "http://127.0.0.1:8000/api"
USER_EMAIL = "test@test.com"
USER_PASSWORD = "test123"

# Data map to retry attachment
RECIPES_TO_FIX = [
    {
        "name": "Piletina s rižom i brokulom",
        "ingredients": ["Piletina prsa", "Riža (bijela)", "Brokula", "Maslinovo ulje"]
    },
    {
        "name": "Tjestenina s tunom i rajčicom",
        "ingredients": ["Tjestenina", "Tuna (konzerva)", "Rajčica umak"]
    },
    {
        "name": "Zobena kaša s bananom",
        "ingredients": ["Zobene pahuljice", "Mlijeko", "Banana"]
    },
    {
        "name": "Kajgana s tostom i avokadom",
        "ingredients": ["Jaja", "Kruh (integralni)", "Avokado", "Maslinovo ulje"]
    },
    {
        "name": "Losos s krumpirom i špinatom",
        "ingredients": ["Losos", "Krumpir", "Špinat", "Maslinovo ulje"]
    },
    {
        "name": "Proteinski smoothie",
        "ingredients": ["Banana", "Mlijeko", "Jogurt (grčki)", "Bademi"]
    },
    {
        "name": "Salata s piletinom i avokadom",
        "ingredients": ["Piletina prsa", "Avokado", "Špinat", "Maslinovo ulje"]
    }
]

def login():
    print(f"Logging in as {USER_EMAIL}...")
    try:
        response = requests.post(f"{API_URL}/login", json={
            "email": USER_EMAIL,
            "password": USER_PASSWORD
        })
        return response.json().get("token")
    except:
        return None

def get_mapping(token, endpoint):
    headers = {"Authorization": f"Bearer {token}"}
    try:
        items = requests.get(f"{API_URL}/{endpoint}", headers=headers).json()
        # Handle if wrapped in data or direct list
        if isinstance(items, dict) and 'data' in items:
            items = items['data']
        return {item['name']: item['id'] for item in items}
    except Exception as e:
        print(f"Error getting {endpoint}: {e}")
        return {}

def fix_attachments(token):
    print("\n--- Fixing Ingredient Attachments ---")
    headers = {"Authorization": f"Bearer {token}"}
    
    # 1. Get IDs
    ing_map = get_mapping(token, "ingredients")
    recipe_map = get_mapping(token, "recipes")
    
    print(f"Found {len(ing_map)} ingredients and {len(recipe_map)} recipes.")
    
    for recipe_data in RECIPES_TO_FIX:
        r_name = recipe_data["name"]
        if r_name not in recipe_map:
            print(f"⚠️ Recipe not found: {r_name}")
            continue
            
        r_id = recipe_map[r_name]
        ing_ids = []
        
        for ing_name in recipe_data["ingredients"]:
            if ing_name in ing_map:
                ing_ids.append(ing_map[ing_name])
            else:
                print(f"⚠️ Ingredient not found: {ing_name}")
        
        if not ing_ids:
            continue
            
        # 2. Attach using correct payload format
        payload = {
            "ingredient_ids": ing_ids
        }
        
        # Endpoint: /recipes/{recipe}/ingredients  (where {recipe} is the MODEL binding, likely ID)
        url = f"{API_URL}/recipes/{r_id}/ingredients"
        print(f"Attaching {len(ing_ids)} ingredients to '{r_name}' (ID: {r_id})...")
        
        response = requests.post(url, json=payload, headers=headers)
        
        if response.status_code == 200:
            print(f"✅ Success!")
        else:
            print(f"❌ Failed: {response.text}")

if __name__ == "__main__":
    token = login()
    if token:
        fix_attachments(token)
    else:
        print("Login failed")
