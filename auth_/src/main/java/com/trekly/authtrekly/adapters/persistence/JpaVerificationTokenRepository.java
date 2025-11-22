package com.trekly.authtrekly.adapters.persistence;

import com.trekly.authtrekly.core.model.VerificationToken;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface JpaVerificationTokenRepository extends JpaRepository<VerificationToken, Long>, VerificationTokenRepository {

    Optional<VerificationToken> findByToken(String token);
}