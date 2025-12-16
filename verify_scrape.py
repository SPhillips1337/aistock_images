
import requests
from bs4 import BeautifulSoup

url = "https://www.timeanddate.com/holidays/fun/"
headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
}

try:
    response = requests.get(url, headers=headers)
    response.raise_for_status()
    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Based on standard table structure usually found on these sites
    # Looking for the row corresponding to today (approximate logic for verifying content exists)
    rows = soup.find_all('tr')
    print(f"Found {len(rows)} rows")
    
    for row in rows[:20]: # Print first 20 rows to check content
        text = row.get_text(" | ", strip=True)
        if text:
            print(text)

except Exception as e:
    print(f"Error: {e}")
