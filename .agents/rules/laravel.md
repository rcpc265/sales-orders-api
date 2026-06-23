# Reglas del Proyecto Sales Orders API

- **PHPDoc obligatorio:** Todas las clases, métodos públicos y propiedades de DTOs deben incluir anotaciones PHPDoc con tipos estrictos (`@param`, `@return`, `@throws`). Usar PHPDoc especialmente para tipar arrays complejos (ej. `@param array<int, array{product_id: int, quantity: int}> $items`).
- **Código en inglés estricto:** Variables, clases, métodos y columnas de base de datos en inglés. Solo los mensajes JSON dirigidos al usuario final pueden estar en español.
- **Pragmatic Laravel:** Arquitectura Controllers → Form Requests → DTOs → Services. Sin DDD estricto ni Repositories.
