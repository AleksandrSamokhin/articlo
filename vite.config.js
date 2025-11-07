import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss({
            content: [
                "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
                "./storage/framework/views/*.php",
                "./resources/views/**/*.blade.php",
            ],
        }),
    ],
});
