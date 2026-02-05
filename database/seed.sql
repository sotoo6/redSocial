USE redsocial;

INSERT INTO users (name, email, password_hash, role) VALUES
('Profesor Demo', 'profe@demo.com', '$2y$10$S2.3YeTQfUfkCLK5TJj/cOeHFeu/y.Hx7tjt4WRGNa2IYoJ2S9k4G', 'profesor'),
('Alumno Demo', 'alumno@demo.com', '$2y$10$JA4lueqs8h80LU6GeeZw/OK3GEwz1heV9KRlsvNw45brypGpyXulW', 'alumno');