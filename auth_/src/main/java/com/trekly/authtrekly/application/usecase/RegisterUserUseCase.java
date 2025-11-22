package com.trekly.authtrekly.application.usecase;

import com.trekly.authtrekly.adapters.persistence.UserRepository;
import com.trekly.authtrekly.adapters.persistence.VerificationTokenRepository;
import com.trekly.authtrekly.core.model.User;
import com.trekly.authtrekly.core.model.VerificationToken;
import com.trekly.authtrekly.core.service.EmailService;
import com.trekly.authtrekly.core.service.PasswordService;
import com.trekly.authtrekly.core.service.TokenService;

public class RegisterUserUseCase {

    private final UserRepository userRepository;
    private final VerificationTokenRepository tokenRepository;
    private final PasswordService passwordService;
    private final TokenService tokenService;
    private final EmailService emailService;

    public RegisterUserUseCase(UserRepository userRepository, VerificationTokenRepository tokenRepository,
                                PasswordService passwordService, TokenService tokenService, EmailService emailService) {
        this.userRepository = userRepository;
        this.tokenRepository = tokenRepository;
        this.passwordService = passwordService;
        this.tokenService = tokenService;
        this.emailService = emailService;
    }

    public void registerUser(String email, String plainPassword, String fullName, String phone) {
        // Validate email and password
        if (userRepository.existsByEmail(email)) {
            throw new IllegalArgumentException("Email already in use");
        }

        // Hash password
        String hashedPassword = passwordService.hashPassword(plainPassword);

        // Create user
        User user = new User();
        user.setEmail(email);
        user.setPassword(hashedPassword);
        user.setFullName(fullName);
        user.setPhone(phone);
        user.setStatus(User.UserStatus.PENDING_VERIFICATION);
        userRepository.save(user);

        // Generate verification token
        VerificationToken token = new VerificationToken();
        token.setToken(tokenService.generateUuidToken());
        token.setUserId(user.getId());
        token.setExpiryDate(java.time.LocalDateTime.now().plusHours(24));
        tokenRepository.save(token);

        // Send verification email
        String verificationLink = "https://your-frontend.com/verify-email?token=" + token.getToken();
        emailService.sendEmail(email, "Verify your email", "Click the link to verify: " + verificationLink);
    }
}