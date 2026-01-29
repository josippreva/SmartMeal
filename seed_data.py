import requests
import json

# Configuration
API_URL = "http://127.0.0.1:8000/api"
USER_EMAIL = "test@test.com"
USER_PASSWORD = "test123"

# Data to seed
INGREDIENTS = [
    {"name": "Piletina prsa", "calories": 165, "protein": 31, "carbs": 0, "fat": 3.6},
    {"name": "Riža (bijela)", "calories": 130, "protein": 2.7, "carbs": 28, "fat": 0.3},
    {"name": "Brokula", "calories": 34, "protein": 2.8, "carbs": 7, "fat": 0.4},
    {"name": "Maslinovo ulje", "calories": 884, "protein": 0, "carbs": 0, "fat": 100},
    {"name": "Tuna (konzerva)", "calories": 116, "protein": 26, "carbs": 0, "fat": 1},
    {"name": "Tjestenina", "calories": 131, "protein": 5, "carbs": 25, "fat": 1.1},
    {"name": "Rajčica umak", "calories": 29, "protein": 1.3, "carbs": 6, "fat": 0.2},
    {"name": "Zobene pahuljice", "calories": 389, "protein": 16.9, "carbs": 66, "fat": 6.9},
    {"name": "Mlijeko", "calories": 42, "protein": 3.4, "carbs": 5, "fat": 1},
    {"name": "Banana", "calories": 89, "protein": 1.1, "carbs": 23, "fat": 0.3},
    {"name": "Jaja", "calories": 155, "protein": 13, "carbs": 1.1, "fat": 11},
    {"name": "Kruh (integralni)", "calories": 247, "protein": 13, "carbs": 41, "fat": 3.4},
    {"name": "Avokado", "calories": 160, "protein": 2, "carbs": 9, "fat": 15},
    {"name": "Sir (svježi)", "calories": 98, "protein": 11, "carbs": 3.4, "fat": 4.3},
    {"name": "Jabuka", "calories": 52, "protein": 0.3, "carbs": 14, "fat": 0.2},
    {"name": "Bademi", "calories": 579, "protein": 21, "carbs": 22, "fat": 50},
    {"name": "Losos", "calories": 208, "protein": 20, "carbs": 0, "fat": 13},
    {"name": "Krumpir", "calories": 77, "protein": 2, "carbs": 17, "fat": 0.1},
    {"name": "Špinat", "calories": 23, "protein": 2.9, "carbs": 3.6, "fat": 0.4},
    {"name": "Jogurt (grčki)", "calories": 59, "protein": 10, "carbs": 3.6, "fat": 0.4}
]

RECIPES = [
    {
        "name": "Piletina s rižom i brokulom",
        "description": "Klasično bodybuildersko jelo, puno proteina i zdravih ugljikohidrata.",
        "instructions": "Skuhajte rižu. Piletinu ispecite na tavi s malo ulja. Brokulu skuhajte na pari. Pomiješajte sve zajedno.",
        "calories": 450,
        "protein": 40,
        "carbs": 50,
        "fat": 10,
        "ingredients": ["Piletina prsa", "Riža (bijela)", "Brokula", "Maslinovo ulje"]
    },
    {
        "name": "Tjestenina s tunom i rajčicom",
        "description": "Brzi ručak gotov za 15 minuta.",
        "instructions": "Skuhajte tjesteninu. Pomiješajte tunu s umakom od rajčice i zagrijte. Prelijte preko tjestenine.",
        "calories": 520,
        "protein": 35,
        "carbs": 70,
        "fat": 8,
        "ingredients": ["Tjestenina", "Tuna (konzerva)", "Rajčica umak"]
    },
    {
        "name": "Zobena kaša s bananom",
        "description": "Savršen doručak za energiju.",
        "instructions": "Skuhajte zobene pahuljice u mlijeku. Dodajte narezanu bananu na vrh.",
        "calories": 380,
        "protein": 15,
        "carbs": 65,
        "fat": 6,
        "ingredients": ["Zobene pahuljice", "Mlijeko", "Banana"]
    },
    {
        "name": "Kajgana s tostom i avokadom",
        "description": "Doručak bogat proteinima i zdravim mastima.",
        "instructions": "Ispecite jaja. Tostirajte kruh. Narežite avokado. Poslužite zajedno.",
        "calories": 480,
        "protein": 22,
        "carbs": 35,
        "fat": 25,
        "ingredients": ["Jaja", "Kruh (integralni)", "Avokado", "Maslinovo ulje"]
    },
    {
        "name": "Losos s krumpirom i špinatom",
        "description": "Večera bogata omega-3 masnim kiselinama.",
        "instructions": "Ispecite losos i krumpir u pećnici. Špinat kratko popržite na tavi.",
        "calories": 550,
        "protein": 35,
        "carbs": 40,
        "fat": 25,
        "ingredients": ["Losos", "Krumpir", "Špinat", "Maslinovo ulje"]
    },
    {
        "name": "Proteinski smoothie",
        "description": "Brzi međuobrok poslije treninga.",
        "instructions": "U blender stavite bananu, mlijeko, grčki jogurt i bademe. Miksajte dok ne postane glatko.",
        "calories": 320,
        "protein": 25,
        "carbs": 35,
        "fat": 12,
        "ingredients": ["Banana", "Mlijeko", "Jogurt (grčki)", "Bademi"]
    },
    {
        "name": "Salata s piletinom i avokadom",
        "description": "Lagani ručak s malo ugljikohidrata (Low Carb).",
        "instructions": "Ispecite piletinu. Narežite povrće i avokado. Sve pomiješajte i začinite maslinovim uljem.",
        "calories": 410,
        "protein": 35,
        "carbs": 10,
        "fat": 28,
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
        
        if response.status_code == 200:
            token = response.json().get("token")
            print("Login successful!")
            return token
        else:
            print(f"Login failed: {response.text}")
            return None
    except Exception as e:
        print(f"Connection error: {str(e)}")
        return None

def create_ingredients(token):
    print("\n--- Creating Ingredients ---")
    headers = {"Authorization": f"Bearer {token}"}
    ingredient_map = {} # Name -> ID mapping
    
    # First get existing ingredients to avoid duplicates
    try:
        existing = requests.get(f"{API_URL}/ingredients", headers=headers).json()
        for ing in existing:
            ingredient_map[ing['name']] = ing['id']
            # Also map lowercase just in case
            ingredient_map[ing['name'].lower()] = ing['id']
    except:
        pass

    for ing_data in INGREDIENTS:
        # Check if exists
        if ing_data['name'] in ingredient_map:
            print(f"Ingredient '{ing_data['name']}' already exists (ID: {ingredient_map[ing_data['name']]})")
            continue
            
        # Create request needed format: name, calories_per_100g etc.
        # Check what the API expects. Looking at IngredientController::store
        # Assuming simple mapping for now based on what I see in migration
        payload = {
            "name": ing_data["name"],
            # Assuming these columns were added to migration
            "calories_per_100g": ing_data["calories"],
            "protein_per_100g": ing_data["protein"],
            "carbs_per_100g": ing_data["carbs"],
            "fat_per_100g": ing_data["fat"]
        }
        
        response = requests.post(f"{API_URL}/ingredients", json=payload, headers=headers)
        
        if response.status_code in [200, 201]:
            new_id = response.json().get("id")
            ingredient_map[ing_data['name']] = new_id
            print(f"✅ Created ingredient: {ing_data['name']}")
        else:
            print(f"❌ Failed to create {ing_data['name']}: {response.text}")
            
    return ingredient_map

def create_recipes(token, ingredient_map):
    print("\n--- Creating Recipes ---")
    headers = {"Authorization": f"Bearer {token}"}
    
    for recipe_data in RECIPES:
        # 1. Create Recipe
        payload = {
            "name": recipe_data["name"],
            # "description": recipe_data["description"], # Trenutno nije podržano u API
            # "instructions": recipe_data["instructions"], # Trenutno nije podržano u API
            "calories": recipe_data["calories"],
            "protein": recipe_data["protein"],
            "carbs": recipe_data["carbs"],
            "fat": recipe_data["fat"],
            "prep_time": 30, # Ispravljeno ime polja
            # "cooking_time": 15 
        }
        
        response = requests.post(f"{API_URL}/recipes", json=payload, headers=headers)
        
        if response.status_code in [200, 201]:
            recipe_id = response.json().get("id")
            print(f"✅ Created recipe: {recipe_data['name']} (ID: {recipe_id})")
            
            # 2. Attach Ingredients
            for ing_name in recipe_data["ingredients"]:
                ing_id = ingredient_map.get(ing_name)
                if ing_id:
                    # Attach ingredient to recipe
                    # Assuming endpoint /recipes/{recipe}/ingredients
                    # Payload usually needs quantity/unit
                    attach_payload = {
                        "ingredient_id": ing_id,
                        "quantity": 100, # Default 100g
                        "unit": "g"
                    }
                    requests.post(f"{API_URL}/recipes/{recipe_id}/ingredients", json=attach_payload, headers=headers)
            
        else:
            print(f"❌ Failed to create recipe {recipe_data['name']}: {response.text}")

def main():
    token = login()
    if token:
        ing_map = create_ingredients(token)
        create_recipes(token, ing_map)
        print("\n🎉 Seeding complete!")

if __name__ == "__main__":
    main()
