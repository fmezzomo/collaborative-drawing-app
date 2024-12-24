Collaborative Drawing App
This is a collaborative real-time drawing app where multiple users can join a shared canvas, draw, and see each other's strokes in real-time. Each user is represented by a unique color and name, and all updates are broadcasted to everyone connected to the same canvas.

Features
Real-time drawing: All users can draw on the canvas and see each other's strokes instantly.
User presence: Users are displayed with a unique color and name to identify who is drawing.
Tool selection: Users can select drawing tools like text or rectangles to add objects to the canvas.
Canvas updates: Any changes made to the canvas (add, move, modify, or delete objects) are shared with all users in real-time.

#Installation

1. Clone the repository
git clone https://github.com/yourusername/collaborative-drawing-app.git
cd collaborative-drawing-app

2. Install dependencies
Backend (Laravel)
Install Composer dependencies:
composer install

3. Migrate the database:
php artisan migrate

4. Frontend (Vue.js)
Install npm dependencies:
npm install

5. Run the development server:
npm run dev

6. Run Websocket server
php artisan reverb:start
php artisan queue:work