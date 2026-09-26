-- phpMyAdmin эсвэл `mysql -u root -p` ашиглан ажиллуулна.
-- 1) Эхлээд базаа үүсгэнэ (хэрэв байхгүй бол):
CREATE DATABASE IF NOT EXISTS map_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE map_app;

-- 2) Үндсэн хүснэгт
CREATE TABLE IF NOT EXISTS people (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(255)  NOT NULL,
  phone        VARCHAR(50)   DEFAULT NULL,

  org1_address VARCHAR(500)  DEFAULT NULL,
  org1_lat     DECIMAL(10,7) DEFAULT NULL,
  org1_lng     DECIMAL(10,7) DEFAULT NULL,
  org1_precision ENUM('exact','khoroo','district','manual') DEFAULT NULL,

  org2_address VARCHAR(500)  DEFAULT NULL,
  org2_lat     DECIMAL(10,7) DEFAULT NULL,
  org2_lng     DECIMAL(10,7) DEFAULT NULL,
  org2_precision ENUM('exact','khoroo','district','manual') DEFAULT NULL,

  created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Хэрэв өмнө нь schema-г import хийсэн бөгөөд зөвхөн шинэ баганыг нэмэх шаардлагатай бол
-- дараах мөрүүдийг ганцаараа ажиллуулж болно (хүснэгт аль хэдийн байгаа тохиолдолд):
-- ALTER TABLE people ADD COLUMN org1_precision ENUM('exact','khoroo','district','manual') DEFAULT NULL AFTER org1_lng;
-- ALTER TABLE people ADD COLUMN org2_precision ENUM('exact','khoroo','district','manual') DEFAULT NULL AFTER org2_lng;

-- 3) Жишээ өгөгдөл (сонголтоор устгаж болно)
INSERT INTO people (name, phone, org1_address, org2_address) VALUES
('Батболд', '99001122', 'Сүхбаатарын талбай, Улаанбаатар', 'Их сургуулийн гудамж, Улаанбаатар'),
('Сарнай',  '88117733', 'Нархан зах, Улаанбаатар',          'Чингисийн өргөн чөлөө 15, Улаанбаатар'),
('Төмөр',   '95556677', 'Зайсан толгой, Улаанбаатар',       'Дамбадаржаа, Улаанбаатар');

-- org1_lat/org1_lng, org2_lat/org2_lng багануудыг заавал бөглөх шаардлагагүй:
-- хоосон орхивол api/get-people.php эхний удаа уншихдаа автоматаар geocode хийж,
-- олсон солбицлыг эргүүлж DB-рүү хадгална (дараагийн удаа дахин geocode хийхгүй).
