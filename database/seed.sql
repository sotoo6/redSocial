USE redsocial;

INSERT INTO users (name, email, password_hash, role) VALUES
('Profesor Demo', 'profesor@gmail.com', '$2y$10$S2.3YeTQfUfkCLK5TJj/cOeHFeu/y.Hx7tjt4WRGNa2IYoJ2S9k4G', 'profesor'),
('Alumno Demo', 'alumno@gmail.com', '$2y$10$JA4lueqs8h80LU6GeeZw/OK3GEwz1heV9KRlsvNw45brypGpyXulW', 'alumno');

INSERT INTO messages (idUser, subject, content, status, isDeleted, deletedAt) VALUES 
(1, 'Desarrollo Web Entorno Servidor', 'Mensaje de ejemplo para DWES.', 'published', 0, NULL),
(1, 'Desarrollo Web Entorno Cliente', 'Mensaje de ejemplo para DWEC.', 'published', 0, NULL),
(2, 'Diseño Interfaces', 'Mensaje de ejemplo para DI.', 'pending', 0, NULL),
(2, 'Digitalización', 'Mensaje de ejemplo para Digitalización.', 'rejected', 0, NULL);