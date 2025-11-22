package com.trekly.authtrekly.core.service;

import java.time.Instant;
import java.util.Date;
import java.util.UUID;

import javax.crypto.SecretKey;

import org.springframework.stereotype.Service;

import io.jsonwebtoken.Claims;
import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.SignatureAlgorithm;
import io.jsonwebtoken.security.Keys;

@Service
public class TokenService {

    private final String jwtSecret = "your-256-bit-secret-key-goes-here-your-256-bit-secret-key-goes-here"; // Replace with a secure key
    private final SecretKey signingKey = Keys.hmacShaKeyFor(jwtSecret.getBytes());

    public String generateJwtToken(UUID userId, String email, String[] roles, long expirationMillis) {
        Instant now = Instant.now();
        return Jwts.builder()
                .setSubject(userId.toString())
                .claim("email", email)
                .claim("roles", roles)
                .setIssuedAt(Date.from(now))
                .setExpiration(Date.from(now.plusMillis(expirationMillis)))
                .signWith(signingKey, SignatureAlgorithm.HS256)
                .compact();
    }

    public boolean validateJwtToken(String token) {
        try {
            Jwts.parserBuilder().setSigningKey(signingKey).build().parseClaimsJws(token);
            return true;
        } catch (Exception e) {
            return false;
        }
    }

    public UUID generateUuidToken() {
        return UUID.randomUUID();
    }

    public String refreshToken(String refreshToken) {
        try {
            Claims claims = Jwts.parserBuilder()
                    .setSigningKey(signingKey)
                    .build()
                    .parseClaimsJws(refreshToken)
                    .getBody();

            String userId = claims.getSubject();
            String email = claims.get("email", String.class);
            String[] roles = claims.get("roles", String[].class);

            // Generate a new token with the same claims but a new expiration
            return generateJwtToken(UUID.fromString(userId), email, roles, 3600000); // 1 hour expiration
        } catch (Exception e) {
            throw new IllegalArgumentException("Invalid refresh token", e);
        }
    }
}