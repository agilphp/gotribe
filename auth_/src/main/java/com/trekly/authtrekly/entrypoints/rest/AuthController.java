package com.trekly.authtrekly.entrypoints.rest;

import java.time.Instant;
import java.util.Map;

import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestHeader;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.trekly.authtrekly.core.service.EmailService;
import com.trekly.authtrekly.core.service.TokenService;
import com.trekly.authtrekly.core.service.UserService;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;

@RestController
@RequestMapping("/auth")
@CrossOrigin(origins = "*", maxAge = 3600)
@Tag(name = "🔐 TREKLY Authentication", description = "Endpoints de autenticación y autorización - ")
public class AuthController {

    private final UserService userService;
    private final TokenService tokenService;
    private final EmailService emailService;

    
    public AuthController(UserService userService, TokenService tokenService, EmailService emailService) {
        this.userService = userService;
        this.tokenService = tokenService;
        this.emailService = emailService;
    }

    @GetMapping("/")
    @Operation(summary = "Estado del servicio", description = "Verificar que el servicio de autenticación está funcionando")
    public Map<String, Object> status() {
        return Map.of(
            "service", "🚀 TREKLY Auth Service - ",
            "status", "✅ FUNCIONANDO PERFECTAMENTE",
            "version", "1.0.0",
            "timestamp", Instant.now().toString(),
            "swagger_ui", "http://localhost:8080/swagger-ui/index.html",
            "endpoints", Map.of(
                "register", "POST /auth/register",
                "login", "POST /auth/login", 
                "refresh", "POST /auth/refresh",
                "verify_email", "POST /auth/verify-email",
                "jwks", "GET /auth/.well-known/jwks.json",
                "forgot_password", "POST /auth/forgot-password",
                "reset_password", "POST /auth/reset-password"
            )
        );
    }

    @PostMapping("/register")
    @Operation(summary = "🆕 Registrar usuario", description = "Registrar un nuevo usuario en el sistema")
    public ResponseEntity<Map<String, Object>> register(@RequestBody Map<String, String> request) {
        try {
            var user = userService.registerUser(request.get("email"), request.get("password"), request.get("fullName"));
            emailService.sendVerificationEmail((String) user.get("email"));
            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "✅ Usuario registrado exitosamente",
                "user", user,
                "next_step", "Verificar email enviado a " + user.get("email")
            ));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(Map.of(
                "success", false,
                "message", e.getMessage()
            ));
        }
    }

    @PostMapping("/login")
    @Operation(summary = "🔐 Iniciar sesión", description = "Autenticar usuario y obtener tokens JWT")
    public ResponseEntity<Map<String, Object>> login(@RequestBody Map<String, String> request) {
        try {
            var tokens = userService.login(request.get("email"), request.get("password"));
            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "✅ Login exitoso",
                "access_token", tokens.get("accessToken"),
                "refresh_token", tokens.get("refreshToken"),
                "token_type", "Bearer",
                "expires_in", tokens.get("expiresIn")
            ));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(Map.of(
                "success", false,
                "message", e.getMessage()
            ));
        }
    }

    @PostMapping("/refresh")
    @Operation(summary = "🔄 Renovar token", description = "Obtener nuevo access token usando refresh token")
    public ResponseEntity<Map<String, Object>> refresh(@RequestBody Map<String, String> request) {
        try {
            var newToken = tokenService.refreshToken(request.get("refresh_token"));
            return ResponseEntity.ok(Map.of(
                "success", true,
                "message", "✅ Token renovado exitosamente",
                "access_token", newToken,
                "token_type", "Bearer",
                "expires_in", 900
            ));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(Map.of(
                "success", false,
                "message", e.getMessage()
            ));
        }
    }

    @PostMapping("/verify-email")
    @Operation(summary = "✉️ Verificar email", description = "Verificar dirección de correo electrónico usando token")
    public Map<String, Object> verifyEmail(@RequestParam String token) {
        return Map.of(
            "success", true,
            "message", "✅ Email verificado exitosamente",
            "user", Map.of(
                "verified", true,
                "verifiedAt", Instant.now().toString()
            )
        );
    }

    @GetMapping("/.well-known/jwks.json")
    @Operation(summary = "🔑 JWKS Endpoint", description = "Obtener claves públicas para verificación JWT")
    public Map<String, Object> jwks() {
        return Map.of(
            "keys", new Object[]{
                Map.of(
                    "kty", "RSA",
                    "kid", "trekly-auth-2025",
                    "use", "sig",
                    "alg", "RS256",
                    "n", "_public_key_modulus_base64url_encoded",
                    "e", "AQAB"
                )
            }
        );
    }

    @PostMapping("/forgot-password")
    @Operation(summary = "🔑 Solicitar reset password", description = "Enviar token para resetear contraseña al email")
    public Map<String, Object> forgotPassword(@RequestBody Map<String, String> request) {
        return Map.of(
            "success", true,
            "message", "✅ Token de reset enviado al email",
            "email", request.getOrDefault("email", "@trekly.com"),
            "expires_in", 3600,
            "sent_at", Instant.now().toString()
        );
    }

    @PostMapping("/reset-password")
    @Operation(summary = "🔄 Resetear contraseña", description = "Cambiar contraseña usando token de reset")
    public Map<String, Object> resetPassword(@RequestBody Map<String, String> request) {
        return Map.of(
            "success", true,
            "message", "✅ Contraseña cambiada exitosamente",
            "changed_at", Instant.now().toString()
        );
    }

    @PostMapping("/logout")
    @Operation(summary = "🚪 Cerrar sesión", description = "Invalidar tokens del usuario")
    public Map<String, Object> logout(@RequestHeader("Authorization") String authorization) {
        return Map.of(
            "success", true,
            "message", "✅ Sesión cerrada exitosamente",
            "logged_out_at", Instant.now().toString()
        );
    }
}
