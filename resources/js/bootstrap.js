import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Global error handler for axios requests (419, 404, etc.)
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        // Handle specific HTTP error codes that should redirect to landing page
        if (error.response) {
            const status = error.response.status;
            
            // Redirect to landing page for these errors
            if ([419, 404, 403, 409, 401].includes(status)) {
                window.location.href = '/';
                return Promise.reject(error);
            }
        }
        
        // For network errors or other issues, still reject
        return Promise.reject(error);
    }
);
