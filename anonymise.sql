UPDATE users
SET
    facebook_id = NULL,
    username = CONCAT('john', id, 'doe'),
    encrypted_password = '$2y$12$eS7dFH02rFSUn8dqi78sZ.VjMsXsuw0ly2KaxIsqhu7HDrhm4g0cG',
    email = CONCAT('john', id, '@doe.fr'),
    twitter = null,
    site = null,
    facebook = null,
    recover_token = null,
    last_sign_in_at = null,
    last_sign_in_ip = '0.0.0.0',
    reset_password_sent_at = null,
    reset_password_token = null,
    current_sign_in_ip = '0.0.0.0',
    github_id = null,
    google_id = null,
    company_info = null,
    discord_id = null
WHERE 1 = 1
