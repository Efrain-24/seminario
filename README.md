# 🐟 Sistema de Gestión Piscícola - Beyond Learning

## 📋 Descripción del Proyecto

**Beyond Learning** es un sistema integral de gestión para cultivos piscícolas desarrollado como parte del seminario académico. Este software permite administrar de manera eficiente todos los aspectos relacionados con la piscicultura, desde la gestión de lotes hasta el monitoreo ambiental.

## ✨ Características Principales

### 🎯 Módulos del Sistema
- **📊 Gestión de Lotes**: Control completo de cada grupo de organismos desde su siembra hasta su cosecha
- **🍃 Alimentación**: Planificación y registro de raciones, tipos de alimento y conversión alimenticia
- **🌊 Monitoreo Ambiental**: Seguimiento de parámetros de calidad del agua y condiciones ambientales
- **🏥 Sanidad y Bioseguridad**: Control de enfermedades, tratamientos y protocolos de bioseguridad

### 🚀 Tecnologías Utilizadas
- **Backend**: Laravel 12 (PHP Framework)
- **Frontend**: Blade Templates + Tailwind CSS
- **Autenticación**: Laravel Breeze
- **Base de Datos**: SQLite (desarrollo) / MySQL (producción)
- **Herramientas**: Vite, Node.js, Composer

## 📦 Instalación

### Requisitos Previos
- PHP >= 8.2
- Composer
- Node.js >= 18
- SQLite o MySQL

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/Efrain-24/seminario.git
   cd seminario
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js**
   ```bash
   npm install
   ```

4. **Configurar el archivo de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar la base de datos**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Compilar assets**
   ```bash
   npm run build
   ```

7. **Iniciar el servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## 🎨 Capturas de Pantalla

### Página Principal
- Diseño moderno y responsivo
- Logo personalizado en header
- Módulos organizados en formato 2x2

### Sistema de Autenticación
- Login y registro de usuarios
- Recuperación de contraseñas
- Integración con Laravel Breeze

## 👥 Equipo de Desarrollo

- **Desarrollador Principal**: Efrain-24
- **Institución**: Beyond Learning
- **Proyecto**: Seminario - Sistema de Gestión Piscícola

## 🤝 Contribución

Si deseas contribuir al proyecto:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Contacto

- **GitHub**: [@Efrain-24](https://github.com/Efrain-24)
- **Repositorio**: [seminario](https://github.com/Efrain-24/seminario)

---

### 🐠 Sobre la Piscicultura

La piscicultura es el cultivo controlado de especies acuáticas con fines comerciales o de conservación. Esta actividad permite la producción eficiente de proteína animal de alta calidad mientras se reduce la presión sobre las poblaciones naturales de peces.

**Beneficios principales:**
- ✅ Producción de alimento rico en proteínas y ácidos grasos esenciales
- ✅ Uso eficiente de los recursos hídricos
- ✅ Menor impacto ambiental que otras formas de producción animal
- ✅ Generación de empleo en zonas rurales

---

*© 2025 Beyond Learning - Sistema de Gestión Piscícola. Todos los derechos reservados.*
