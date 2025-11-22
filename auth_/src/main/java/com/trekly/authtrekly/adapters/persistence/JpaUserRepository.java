package com.trekly.authtrekly.adapters.persistence;

import com.trekly.authtrekly.core.model.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;
import java.util.UUID;

@Repository
public interface JpaUserRepository extends JpaRepository<User, UUID>, UserRepository {

    @Override
    default boolean existsByEmail(String email) {
        return findByEmail(email) != null;
    }

    Optional<User> findByEmail(String email);
}