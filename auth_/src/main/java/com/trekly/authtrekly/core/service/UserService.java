package com.trekly.authtrekly.core.service;

import java.util.Map;

import org.springframework.stereotype.Service;

@Service
public class UserService {

    public Map<String, Object> registerUser(String email, String password, String fullName) {
        // Lógica para registrar un usuario
        return Map.of(
            "id", "user_12345",
            "email", email,
            "fullName", fullName,
            "status", "pending_verification",
            "createdAt", System.currentTimeMillis()
        );
    }

    public Map<String, Object> login(String email, String password) {
        // Lógica para autenticar un usuario y generar tokens
        return Map.of(
            "accessToken", "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJ1c2VyXzEyMzQ1IiwiaXNzIjoidHJla2x5LWF1dGgiLCJleHAiOjE3MzIwMzA4MDAsImlhdCI6MTczMjAzMDEwMCwiZW1haWwiOiJkZW1vQHRyZWtseS5jb20ifQ._SIGNATURE",
            "refreshToken", "rt__12345_" + System.currentTimeMillis(),
            "expiresIn", 900
        );
    }
}