import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Urbanist", ...defaultTheme.fontFamily.sans],
            },
        },
        container: {
            center: true,
            padding: "1rem",
        },
        screens: {
            sm: "575px",
            md: "768px",
            lg: "1025px",
            xl: "1170px",
        },
        fontFamily: {
            display: ["Urbanist", ...defaultTheme.fontFamily.sans],
            body: ["Urbanist", ...defaultTheme.fontFamily.sans],
        },
        fontSize: {
            xxs: ["0.75rem"],
            xs: ["0.875rem"],
            sm: ["0.9375rem"],
            base: ["1rem"],
            md: ["1.125rem"],
            lg: ["1.25rem"],
            xl: ["1.5rem"],
            "2xl": ["1.75rem", { lineHeight: "normal" }],
            "3xl": ["2.125rem", { lineHeight: "normal" }],
            "4xl": ["2.5rem", { lineHeight: "normal" }],
            "5xl": ["3rem", { lineHeight: "normal" }],
            "6xl": ["3.5rem", { lineHeight: "normal" }],
            "7xl": ["4rem", { lineHeight: "normal" }],
        },
    },

    plugins: [forms, typography],
};
