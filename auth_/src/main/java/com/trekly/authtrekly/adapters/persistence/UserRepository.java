package com.trekly.authtrekly.adapters.persistence;

import java.util.Optional;
import java.util.UUID;

import com.trekly.authtrekly.core.model.User;

public interface UserRepository {

    boolean existsByEmail(String email);

    Optional<User> findById(UUID id);

    User save(User user);

    Optional<User> findByEmail(String email);
}