document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    const username = document.querySelector('input[name="username"]');
    const phone = document.querySelector('input[name="contact_number"]');
    const role = document.querySelector('select[name="role"]');
    const birthDate = document.querySelector('input[name="date_of_birth"]');
    const password = document.querySelector('input[name="password"]');

    // نام کاربری باید فقط شامل حروف انگلیسی، عدد و _ باشه، ولی نباید تماماً عدد باشه
    const USERNAME_PATTERN = /^[A-Za-z0-9_]+$/;

    function isValidUsername(value) {
        return USERNAME_PATTERN.test(value) && !/^\d+$/.test(value);
    }

    function showError(element, message) {
        let error = element.parentElement.querySelector(".text-danger");

        if (!error) {
            error = document.createElement("span");
            error.className = "text-danger";
            element.parentElement.appendChild(error);
        }

        error.textContent = message;
    }

    function clearError(element) {
        const error = element.parentElement.querySelector(".text-danger");

        if (error) {
            error.textContent = "";
        }
    }

    // ==========================
    // Username
    // ==========================

    username.addEventListener("input", () => {
        const value = username.value;

        if (value === "") {
            showError(username, "نام کاربری الزامی است.");
            return;
        }

        if (!USERNAME_PATTERN.test(value)) {
            showError(
                username,
                "نام کاربری فقط باید شامل حروف انگلیسی، اعداد و _ باشد.",
            );
            return;
        }

        if (/^\d+$/.test(value)) {
            showError(username, "نام کاربری نمی‌تواند فقط شامل عدد باشد.");
            return;
        }

        clearError(username);
    });

    // ==========================
    // Phone
    // ==========================

    phone.addEventListener("input", () => {
        const value = phone.value;

        if (value === "") {
            showError(phone, "شماره موبایل الزامی است.");
            return;
        }

        if (!/^09\d{9}$/.test(value)) {
            showError(phone, "شماره موبایل باید با 09 شروع شود و 11 رقم باشد.");
        } else {
            clearError(phone);
        }
    });

    // ==========================
    // Role
    // ==========================

    role.addEventListener("change", () => {
        if (role.value === "") {
            showError(role, "لطفاً نقش خود را انتخاب کنید.");
        } else {
            clearError(role);
        }
    });

    // ==========================
    // Birth Date
    // ==========================

    birthDate.addEventListener("change", () => {
        if (birthDate.value === "") {
            clearError(birthDate);
            return;
        }

        const year = new Date(birthDate.value).getFullYear();

        if (year < 1900) {
            showError(birthDate, "سال تولد باید بعد از 1900 باشد.");
        } else {
            clearError(birthDate);
        }
    });

    // ==========================
    // Password
    // ==========================

    password.addEventListener("input", () => {
        const value = password.value;

        if (value === "") {
            showError(password, "رمز عبور الزامی است.");
            return;
        }

        if (/[^A-Za-z0-9!@#$%^&*()_+\-=]/.test(value)) {
            showError(
                password,
                "رمز عبور فقط باید شامل کاراکترهای انگلیسی باشد.",
            );
            return;
        }

        let score = 0;

        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[!@#$%^&*()_+\-=]/.test(value)) score++;

        switch (score) {
            case 0:
            case 1:
            case 2:
                showError(password, "قدرت رمز عبور: ضعیف");
                break;

            case 3:
                showError(password, "قدرت رمز عبور: متوسط");
                break;

            case 4:
                showError(password, "قدرت رمز عبور: خوب");
                break;

            case 5:
                showError(password, "قدرت رمز عبور: بسیار قوی");
                break;
        }
    });

    // ==========================
    // Submit Validation
    // ==========================

    form.addEventListener("submit", function (e) {
        let valid = true;

        // Username

        if (!isValidUsername(username.value)) {
            valid = false;

            if (!USERNAME_PATTERN.test(username.value)) {
                showError(username, "نام کاربری معتبر نیست.");
            } else {
                showError(username, "نام کاربری نمی‌تواند فقط شامل عدد باشد.");
            }
        }

        // Phone

        if (!/^09\d{9}$/.test(phone.value)) {
            valid = false;
            showError(phone, "شماره موبایل معتبر نیست.");
        }

        // Role

        if (role.value === "") {
            valid = false;
            showError(role, "انتخاب نقش الزامی است.");
        }

        // Birth Date

        if (birthDate.value !== "") {
            const year = new Date(birthDate.value).getFullYear();

            if (year < 1900) {
                valid = false;
                showError(birthDate, "سال تولد معتبر نیست.");
            }
        }

        // Password

        if (password.value.length < 8) {
            valid = false;
            showError(password, "رمز عبور باید حداقل 8 کاراکتر باشد.");
        }

        if (/[^A-Za-z0-9!@#$%^&*()_+\-=]/.test(password.value)) {
            valid = false;
            showError(
                password,
                "رمز عبور فقط باید شامل کاراکترهای انگلیسی باشد.",
            );
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
