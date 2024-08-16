<?php
include_once("connection/dbcon.php");



?>

<!DOCTYPE html>
<html>

<head>
    <title>Phone Authentication</title>
    <script src="https://www.gstatic.com/firebasejs/9.8.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.8.1/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/ui/6.0.2/firebase-ui-auth.js"></script>
    <link type="text/css" rel="stylesheet" href="https://www.gstatic.com/firebasejs/ui/6.0.2/firebase-ui-auth.css" />
</head>

<body>
    <h1>Phone Authentication</h1>
    <form id="register-form">
        <input type="text" id="phone-number" placeholder="Enter phone number (e.g. +1234567890)" required>
        <div id="recaptcha-container"></div>
        <button type="button" onclick="sendVerificationCode()">Send Verification Code</button>
    </form>

    <form id="verify-form" style="display:none;">
        <input type="text" id="verification-code" placeholder="Enter verification code" required>
        <button type="button" onclick="verifyCode()">Verify</button>
    </form>

    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyAZ6uZVb6AuTRHa-cu9c1yLKchHEefhrPM",
            authDomain: "bagoexpress-1eaf3.firebaseapp.com",
            projectId: "bagoexpress-1eaf3",
            storageBucket: "bagoexpress-1eaf3.appspot.com",
            messagingSenderId: "432608004738",
            appId: "1:432608004738:web:e110e34f16762a1f04029b",
            measurementId: "G-VVCNYMQFSX"
        };
        firebase.initializeApp(firebaseConfig);

        function sendVerificationCode() {
            const phoneNumber = document.getElementById('phone-number').value;
            if (!/^\+[1-9]\d{1,14}$/.test(phoneNumber)) {
                alert("Invalid phone number format. Ensure it starts with a '+' and is followed by the country code and number.");
                return;
            }
            const appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': function (response) {
                    sendCode(phoneNumber, appVerifier);
                }
            });

            appVerifier.render().then(function (widgetId) {
                window.recaptchaWidgetId = widgetId;
                sendCode(phoneNumber, appVerifier);
            }).catch(function (error) {
                console.error("Error rendering reCAPTCHA", error);
            });
        }

        function sendCode(phoneNumber, appVerifier) {
            firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
                .then(function (confirmationResult) {
                    window.confirmationResult = confirmationResult;
                    document.getElementById('register-form').style.display = 'none';
                    document.getElementById('verify-form').style.display = 'block';
                }).catch(function (error) {
                    console.error("Error during signInWithPhoneNumber", error);
                });
        }

        function verifyCode() {
            const code = document.getElementById('verification-code').value;
            window.confirmationResult.confirm(code).then(function (result) {
                const user = result.user;
                alert('Phone number successfully verified');
                window.location="home.php";
            }).catch(function (error) {
                alert('Verification failed. Please try again.');
                console.error("Error during confirmationResult.confirm", error);
            });
        }
    </script>
</body>

</html>