# Stock Photo Website

A professional stock photo website featuring AI-generated images organized by categories.

Demo at https://stock.happymonkey.ai/

## Features

- 📸 **Category-based Gallery**: Browse images organized by themes
- 🔍 **Search Functionality**: Find images by keywords and tags
- 📥 **Download Tracking**: Track popular images
- 📱 **Responsive Design**: Works on desktop, tablet, and mobile
- 🎨 **Modern UI**: Clean, professional design with gradient accents
- ⚡ **Fast Performance**: Lazy loading and optimized assets
- 🤖 **Automated Generation**: Daily image generation with trending topics
- 🔄 **Auto-sync**: Frontend automatically updates with new/deleted images
- 🛡️ **Content Filtering**: NSFW detection and quality validation
- 🧹 **Smart Cleanup**: Automatic database maintenance and broken link prevention

## Technology Stack

- **Backend**: PHP 7.4+ with SQLite database
- **Frontend**: Bootstrap 5, custom CSS with lazy loading
- **AI Generation**: ComfyUI + Ollama models
- **Content Filtering**: Vision AI for NSFW/quality detection
- **Automation**: Cron jobs for scheduled tasks
- **Fonts**: Inter & Poppins from Google Fonts
- **Icons**: Bootstrap Icons

## Directory Structure

```
stock_photos/
├── images/                 # Generated images
├── public/                 # Web root
│   ├── index.php          # Homepage
│   ├── category.php       # Category gallery
│   ├── image.php          # Image detail
│   ├── search.php         # Search results
│   ├── about.php          # About page
│   ├── contact.php        # Contact page
│   ├── download.php       # Download handler
│   └── assets/            # CSS, JS, images
│       ├── css/           # Stylesheets + lazy loading
│       └── js/            # JavaScript + lazy loading
├── includes/              # Shared PHP components
│   ├── config.php         # Database & helpers with file filtering
│   ├── header.php         # Navigation
│   └── footer.php         # Footer
├── scripts/               # Utility scripts
│   ├── index_images.php   # Image indexer
│   └── cleanup_deleted.php # Remove deleted images + fix thumbnails
├── auto_stock_creator.py  # AI image generation with NSFW filtering
├── run_automation.sh      # Full automation pipeline
├── run_daily.sh          # Legacy daily runner
├── run_now.sh            # Manual trigger
├── schema.sql            # Database schema
├── project.json          # Project documentation schema
├── .env                  # Configuration (not in git)
├── .env-example          # Configuration template
└── stock_photos.db       # SQLite database
```

## Setup

1. **Run the setup script**:
   ```bash
   ./setup.sh
   ```

2. **Configure environment variables**:
   Copy `.env-example` to `.env` and update with your API endpoints

3. **Install Python dependencies**:
   ```bash
   ./venv/bin/pip install requests websocket-client feedparser pillow beautifulsoup4 python-dotenv
   ```

4. **Start the development server**:
   ```bash
   cd public && php -S localhost:8000
   ```

5. **Open in browser**:
   ```
   http://localhost:8000
   ```

## Automation

The system includes automated image generation and database management:

### Cron Jobs (automatically configured):
- **Daily Generation** (3 AM): Full automation pipeline with NSFW filtering
- **Cleanup** (every 6 hours): Remove deleted images + fix broken thumbnails
- **Re-indexing** (hourly): Add manually uploaded images

### Manual Commands:

**Generate images and update website:**
```bash
./run_now.sh
```

**Legacy daily runner:**
```bash
./run_daily.sh
```

**Just index new images:**
```bash
php scripts/index_images.php
```

**Clean up deleted images and fix thumbnails:**
```bash
php scripts/cleanup_deleted.php
```

## Configuration

### Environment Variables (.env):
```
COMFYUI_URL=your_comfyui_endpoint
OLLAMA_URL=your_ollama_endpoint
OLLAMA_TEXT_MODEL=your_text_model
OLLAMA_VISION_MODEL=your_vision_model
BBC_RSS_URL=news_feed_url
HOLIDAYS_URL=holiday_scraping_url
USER_AGENT=web_scraping_user_agent
```

### Other Settings:
Edit `includes/config.php` to change:
- Site name
- Contact email
- Database path

## Content Quality & Safety

- **NSFW Detection**: Vision AI automatically rejects inappropriate content
- **Quality Validation**: Checks for anatomical correctness, text legibility, image clarity
- **Professional Standards**: Ensures content is suitable for business use
- **Broken Link Prevention**: Frontend filters out missing images automatically
- **Thumbnail Management**: Auto-fixes broken category thumbnails

## Image Management

- **Adding Images**: Place in `images/` directory, run indexer
- **Deleting Images**: Delete file, cleanup runs automatically
- **Categories**: Auto-extracted from filename (e.g., `Nature_Scene.png` → "Nature Scene")
- **Models**: Supports both "turbo" and "ovis" workflows
- **Quality Control**: All images validated before publication

## Performance Features

- **Lazy Loading**: Images load as user scrolls for faster page loads
- **Responsive Design**: Optimized for all device sizes
- **Database Optimization**: Efficient queries with file existence checks
- **Caching Friendly**: Static assets with proper headers

## Logs

- `automation.log` - Main automation pipeline
- `cleanup.log` - Deleted image cleanup and thumbnail fixes
- `index.log` - Image indexing operations
- `daily_run.log` - Legacy daily runner

## Pages

- **Home** (`/`): Hero section, recent images, category grid
- **Category** (`/category?slug=...`): All images in a category
- **Image** (`/image?id=...`): Image detail with download
- **Search** (`/search?q=...`): Search results
- **About** (`/about`): Information about the site
- **Contact** (`/contact`): Contact form (mailto)

## Design

The website features a modern, professional design with:
- Purple/blue gradient color scheme
- Smooth hover effects and transitions
- Card-based layouts with lazy loading
- Responsive grid system
- Clean typography

## License

All images are AI-generated. See the About page for usage information.
