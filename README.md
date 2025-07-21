# 🎓 ລະບົບລົງທະບຽນນັກສຶກສາ (Student Registration System)

ລະບົບການລົງທະບຽນນັກສຶກສາສຳລັບວິທະຍາໄລການສຶກສາ ອົງຕື້ສັງຄະ

## ✨ ຄຸນສົມບັດ (Features)

- 📝 ລົງທະບຽນນັກສຶກສາໃໝ່
- 📱 ສ້າງ QR Code ສຳລັບແຕ່ລະນັກສຶກສາ
- 🎯 ສ້າງບັດນັກສຶກສາ PDF
- 📊 ຈັດການຂໍ້ມູນນັກສຶກສາ
- 🔍 ຄົ້ນຫາ ແລະ ການຄັດກອງ
- 📷 ອັບໂຫຼດຮູບຖ່າຍ

## 🛠️ ເຕັກໂນໂລຊີ (Technologies)

- **Backend**: PHP 8.x, MySQL
- **Frontend**: HTML5, CSS3, JavaScript, Tailwind CSS
- **PDF Generation**: mPDF
- **QR Code**: API Service
- **Icons**: Font Awesome
- **Alerts**: SweetAlert2

## 📋 ຄວາມຕ້ອງການ (Requirements)

- PHP 8.0 ຫຼື ສູງກວ່າ
- MySQL 5.7 ຫຼື ສູງກວ່າ
- Apache/Nginx
- Composer
- mPDF Library

## 🚀 ການຕິດຕັ້ງ (Installation)

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/register-learning.git
   cd register-learning
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database setup**
   - Import `database/register-learning.sql`
   - Configure database connection in `config/config.php`

4. **Set permissions**
   ```bash
   chmod 755 public/uploads/
   chmod 755 public/downloads/
   ```

5. **Access the application**
   - Navigate to `http://localhost/register-learning`

## 📁 ໂຄງສ້າງໂປລເຈັກ (Project Structure)

```
register-learning/
├── config/
│   ├── config.php
│   └── database.php
├── public/
│   ├── assets/
│   ├── uploads/
│   └── index.php
├── src/
│   ├── classes/
│   ├── controllers/
│   └── helpers/
├── templates/
│   ├── components/
│   └── pages/
├── vendor/
└── database/
```

## 🔧 ການຕັ້ງຄ່າ (Configuration)

Copy `config/config.example.php` to `config/config.php` and update:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'register_learning');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 📸 ພາບໜ້າຈໍ (Screenshots)

[Add screenshots here]

## 🤝 ການປະກອບສ່ວນ (Contributing)

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👥 ທີມພັດທະນາ (Development Team)

- **Developer**: Your Name
- **Email**: your.email@example.com
- **GitHub**: [@yourusername](https://github.com/yourusername)

## 🐛 ລາຍງານປັນຫາ (Bug Reports)

If you find a bug, please create an issue on GitHub with:
- Bug description
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)

## 📞 ສຳລັບການສະໜັບສະໜູນ (Support)

For support, email your.email@example.com or create an issue on GitHub.

---

Made with ❤️ for Education