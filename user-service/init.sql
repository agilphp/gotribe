CREATE TABLE IF NOT EXISTS user_profiles (
    user_id VARCHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar_url VARCHAR(255),
    location VARCHAR(255)
);
CREATE TABLE IF NOT EXISTS user_profiles (
    user_id VARCHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar_url VARCHAR(255),
    location VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS creator_profiles (
    user_id VARCHAR(36) PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    document_info VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    experience_years INT,
    average_rating DECIMAL(3, 2) DEFAULT 0,
    total_ratings INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user_profiles(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS creator_ratings (
    id VARCHAR(36) PRIMARY KEY,
    creator_id VARCHAR(36) NOT NULL,
    member_id VARCHAR(36) NOT NULL,
    project_id VARCHAR(36) NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rating (member_id, project_id)
);
