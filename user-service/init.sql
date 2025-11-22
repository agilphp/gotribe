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
    verified TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user_profiles(user_id) ON DELETE CASCADE
);
