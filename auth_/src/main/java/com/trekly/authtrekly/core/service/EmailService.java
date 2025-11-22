package com.trekly.authtrekly.core.service;

public interface EmailService {

    void sendEmail(String to, String subject, String body);

    default void sendVerificationEmail(String email) {
        String subject = "Verificación de correo electrónico - TREKLY";
        String body = "Gracias por registrarte en TREKLY. Por favor, verifica tu correo electrónico haciendo clic en el enlace proporcionado.";
        sendEmail(email, subject, body);
    }
}