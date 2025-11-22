package com.trekly.authtrekly.application.usecase;

import com.trekly.authtrekly.core.model.User;
import com.trekly.authtrekly.core.model.VerificationToken;
import com.trekly.authtrekly.adapters.persistence.UserRepository;
import com.trekly.authtrekly.adapters.persistence.VerificationTokenRepository;

import java.time.LocalDateTime;

public class VerifyEmailUseCase {

    private final UserRepository userRepository;
    private final VerificationTokenRepository tokenRepository;

    public VerifyEmailUseCase(UserRepository userRepository, VerificationTokenRepository tokenRepository) {
        this.userRepository = userRepository;
        this.tokenRepository = tokenRepository;
    }

    public void verifyEmail(String token) {
        // Find token
        VerificationToken verificationToken = tokenRepository.findByToken(token)
                .orElseThrow(() -> new IllegalArgumentException("Invalid or expired token"));

        // Check expiration
        if (verificationToken.getExpiryDate().isBefore(LocalDateTime.now())) {
            throw new IllegalArgumentException("Token has expired");
        }

        // Find user
        User user = userRepository.findById(verificationToken.getUserId())
                .orElseThrow(() -> new IllegalArgumentException("User not found"));

        // Update user status
        user.setStatus(User.UserStatus.VERIFIED);
        userRepository.save(user);

        // Delete token
        tokenRepository.delete(verificationToken);

        // Emit event (placeholder)
        System.out.println("User verified: " + user.getEmail());
    }
}