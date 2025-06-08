var Constants = {
    get_api_base_url: function () {
        if (location.hostname === "localhost") {
            return "http://localhost/WebProjekat/backend/";
        } else {
            return "https://bloomhaven-flowershop-app-govf2.ondigitalocean.app/";
        }
    },
    USER_ROLE: "user",
    ADMIN_ROLE: "admin"
};
