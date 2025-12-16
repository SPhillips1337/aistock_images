
import requests

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
}

try:
    response = requests.get('https://www.shutterstock.com/trends', headers=headers)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        print(response.text[:500])
    else:
        print("Failed")
except Exception as e:
    print(f"Error: {e}")
