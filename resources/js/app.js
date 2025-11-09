import "./bootstrap";
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

// Initialize Tom Select for multi-select elements
document.addEventListener("DOMContentLoaded", function () {
    const multiSelects = document.querySelectorAll("select[multiple]");
    multiSelects.forEach((select) => {
        new TomSelect(select, {
            plugins: ["remove_button"],
            placeholder: "Select categories...",
            maxItems: null,
        });
    });
});
