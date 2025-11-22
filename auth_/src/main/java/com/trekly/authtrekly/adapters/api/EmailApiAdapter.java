package com.trekly.authtrekly.adapters.api;

import org.springframework.stereotype.Component;

import com.trekly.authtrekly.core.service.EmailService;

@Component
public class EmailApiAdapter implements EmailService {

    @Override
    public void sendEmail(String to, String subject, String body) {
        // Placeholder for external email API integration
        System.out.println("Sending email to: " + to);
        System.out.println("Subject: " + subject);
        System.out.println("Body: " + body);
    }
}