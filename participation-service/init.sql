CREATE TABLE IF NOT EXISTS participations (
    id VARCHAR(36) PRIMARY KEY,
    project_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY unique_participation (project_id, user_id)
);
