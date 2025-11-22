package com.trekly.authtrekly.adapters.persistence;

import java.util.Optional;

import com.trekly.authtrekly.core.model.VerificationToken;

public interface VerificationTokenRepository {

    Optional<VerificationToken> findByToken(String token);

    VerificationToken save(VerificationToken token);

    void delete(VerificationToken token);
}