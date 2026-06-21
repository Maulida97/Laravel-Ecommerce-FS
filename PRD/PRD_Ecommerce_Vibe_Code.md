# Product Requirements Document (PRD)
## E-Commerce Full Vibe Code

**Nama project:**Tokoku.id
**Versi:** 1.0  
**Tanggal:** 15 Juni 2026  
**Status:** Draft  
**Author:** Developer  
**Stack:** Laravel 13 + MySQL + Livewire + Tailwind CSS + Midtrans + Cloudinary

---

## 1. Executive Summary

### 1.1 Visi
Membangun platform e-commerce full-stack dengan pendekatan **vibe coding** — generate sebanyak mungkin codebase dari AI prompts, minimal manual coding, maksimal learning value dan portfolio impact.

### 1.2 Tujuan
| No | Tujuan | Metrik Sukses |
|----|--------|---------------|
| 1 | Generate 80%+ codebase dari AI prompts | < 20% manual code custom |
| 2 | Full CRUD produk, kategori, order | 100% feature functional |
| 3 | Cart & checkout flow lengkap | < 3 step checkout |
| 4 | Payment gateway Midtrans integrated | 100% transaction success rate (test) |
| 5 | Admin dashboard untuk manage store | < 2 click untuk action utama |
| 6 | Responsive + dark mode | Lighthouse mobile score > 90 |
| 7 | Deployable ke production | Deploy time < 30 menit |

### 1.3 Target Market
| Segment | Deskripsi | Pain Point |
|---------|-----------|------------|
| **Pembeli Online** | Usia 18-35, tech-savvy, mobile-first | Checkout ribet, payment limited |
| **Seller/Toko Kecil** | UMKM, individual seller | Platform mahal, setup teknis sulit |
| **Developer (Kamu)** | Yang maintain & extend | Codebase berantakan, dokumentasi minim |

### 1.4 Unique Value Proposition
> "E-commerce yang dibangun dengan vibe coding — cepat, clean, dan fully customizable. Bukan template generic, tapi codebase yang kamu pahami 100% karena dibuat bersama AI."

---

## 2. Scope

### 2.1 In Scope (MVP)

#### Customer Side
- [x] Homepage dengan hero banner, featured products, category browse
- [x] Product catalog dengan filter, sort, search
- [x] Product detail dengan image gallery, variant selector
- [x] Shopping cart (add, adjust qty, remove)
- [x] Checkout flow (shipping form, order summary, payment)
- [x] Order confirmation & status tracking
- [x] Guest checkout (tanpa login)
- [x] Responsive design (mobile, tablet, desktop)
- [x] Dark mode toggle

#### Admin Side
- [x] Dashboard dengan sales overview, recent orders, low stock alerts
- [x] Product management (CRUD + image upload)
- [x] Category management (CRUD)
- [x] Order management (view, update status)
- [x] Settings (store info, payment config)

#### Technical
- [x] Laravel 13 + MySQL
- [x] Livewire 3 untuk reactive UI
- [x] Tailwind CSS + DaisyUI
- [x] Cloudinary untuk image management
- [x] Midtrans Snap untuk payment
- [x] Email notifications
- [x] Database seeders dengan sample data

### 2.2 Out of Scope (Phase 2)
- [ ] Customer authentication & profile
- [ ] Wishlist & reviews
- [ ] Multi-vendor support
- [ ] Advanced analytics & reporting
- [ ] Mobile app (API)
- [ ] Multi-language support
- [ ] Inventory management advanced
- [ ] Shipping integration (JNE, SiCepat, etc.)
- [ ] Affiliate/referral system
- [ ] Live chat support

### 2.3 Assumptions
1. Developer memiliki basic knowledge Laravel & PHP
2. Local development environment sudah siap (PHP 8.3+, MySQL, Node.js)
3. Akun Midtrans sandbox sudah tersedia
4. Akun Cloudinary free tier sudah tersedia
5. Single seller (bukan marketplace)
6. Produk fisik (bukan digital/downloadable)

### 2.4 Constraints
- Budget: $0 (menggunakan free tier semua tools)
- Timeline: 2 minggu untuk MVP
- Hosting awal: localhost only
- Max products: 100 (MVP)
- Max images per product: 5


---

## 3. User Personas

### 3.1 Persona 1: Andi — Pembeli Online
```
Nama: Andi Wijaya
Usia: 24
Pekerjaan: Graphic Designer
Lokasi: Jakarta
Tech Level: High

Goals:
- Cari produk dengan cepat
- Checkout tanpa registrasi
- Bayar dengan metode yang familiar (QRIS, GoPay, OVO)
- Tracking order real-time

Pain Points:
- Website lambat di mobile
- Checkout terlalu banyak step
- Payment gagal tanpa alasan jelas
- Tidak tahu status order

Scenario:
Andi browsing Instagram, lihat produk menarik, klik link ke store.
Dia browse katalog, pilih variant, add to cart, checkout dalam 3 menit.
Bayar pakai QRIS, dapat email confirmation, tracking order via WA.
```

### 3.2 Persona 2: Siti — Seller/Toko Owner
```
Nama: Siti Aminah
Usia: 32
Pekerjaan: Owner UMKM Fashion
Lokasi: Bandung
Tech Level: Low-Medium

Goals:
- Upload produk dengan mudah
- Manage order tanpa ribet
- Lihat penjualan harian
- Cetak invoice untuk pengiriman

Pain Points:
- Platform marketplace potongan besar
- Dashboard platform terlalu kompleks
- Susah customize tampilan
- Data customer tidak bisa diakses

Scenario:
Siti login ke admin panel, lihat dashboard penjualan hari ini.
Upload 5 produk baru dengan foto dari HP, set harga & stok.
Lihat order masuk, update status jadi "processing", cetak invoice.
Kirim paket via JNE, update tracking number.
```

### 3.3 Persona 3: Kamu — Developer
```
Nama: Developer
Usia: 20-30
Pekerjaan: Full Stack Developer
Tech Level: High

Goals:
- Bangun e-commerce dengan cepat via vibe coding
- Pahami 100% codebase yang di-generate
- Punya portfolio project yang impressive
- Bisa extend & customize dengan mudah

Pain Points:
- Template e-commerce terlalu generic
- Codebase orang lain sulit dipahami
- Setup project baru memakan waktu
- Dokumentasi kurang

Scenario:
Kamu buka Bolt/Cursor, paste prompt PRD ini, generate project Laravel.
Review & iterate kode, tambah custom features, deploy ke VPS.
Showcase di portfolio dengan case study lengkap.
```

---

## 4. User Stories & Acceptance Criteria

### 4.1 Epic: Homepage & Discovery

#### US-001: Melihat Homepage
```
Sebagai pembeli,
Saya ingin melihat homepage yang menarik dengan produk unggulan,
Agar saya tertarik untuk berbelanja.

Acceptance Criteria:
- [ ] Hero banner dengan auto-slide (3-5 slides)
- [ ] Featured products section (6 produk)
- [ ] Category browse dengan icon
- [ ] Newsletter signup form
- [ ] Footer dengan links & social media
- [ ] Navbar dengan logo, search, cart icon, dark mode toggle
- [ ] Load time < 2 detik
- [ ] Responsive di mobile (hamburger menu)
```

#### US-002: Browse Product Catalog
```
Sebagai pembeli,
Saya ingin melihat semua produk dengan filter & sort,
Agar saya menemukan produk yang diinginkan.

Acceptance Criteria:
- [ ] Grid view (default) & list view toggle
- [ ] Filter by category (checkbox)
- [ ] Filter by price range (slider)
- [ ] Sort by: newest, price low-high, price high-low, popular
- [ ] Pagination (12 produk per halaman)
- [ ] Product card: image, name, price, rating, badge (sale/new)
- [ ] Quick view modal (optional)
- [ ] URL params untuk share filtered view
```

#### US-003: Search Products
```
Sebagai pembeli,
Saya ingin mencari produk dengan keyword,
Agar saya menemukan produk spesifik.

Acceptance Criteria:
- [ ] Search bar di navbar (sticky)
- [ ] Search by product name & description
- [ ] Autocomplete suggestions (min 3 chars)
- [ ] Search results page dengan filter & sort
- [ ] No results state dengan rekomendasi
- [ ] Search history (localStorage)
```

### 4.2 Epic: Product Detail

#### US-004: View Product Detail
```
Sebagai pembeli,
Saya ingin melihat detail produk lengkap,
Agar saya yakin untuk membeli.

Acceptance Criteria:
- [ ] Image gallery dengan thumbnail & zoom
- [ ] Product name, price, description (HTML)
- [ ] Variant selector (size, color) dengan visual swatch
- [ ] Stock indicator ("In Stock" / "Only X left" / "Out of Stock")
- [ ] Quantity selector (min 1, max stock)
- [ ] Add to cart button
- [ ] Add to wishlist (placeholder)
- [ ] Related products section (4 produk)
- [ ] Breadcrumb navigation
- [ ] Meta tags untuk SEO
```

### 4.3 Epic: Cart & Checkout

#### US-005: Manage Cart
```
Sebagai pembeli,
Saya ingin mengelola item di cart,
Agar saya bisa checkout dengan benar.

Acceptance Criteria:
- [ ] Cart icon di navbar dengan item count badge
- [ ] Mini cart dropdown (hover/click)
- [ ] Full cart page dengan tabel
- [ ] Adjust quantity (+/- buttons)
- [ ] Remove item dengan konfirmasi
- [ ] Subtotal per item & total
- [ ] Empty cart state
- [ ] Cart persisted (session untuk guest, DB untuk logged in)
- [ ] Stock validation saat adjust qty
```

#### US-006: Checkout Process
```
Sebagai pembeli,
Saya ingin checkout dengan cepat & aman,
Agar saya bisa menyelesaikan pembelian.

Acceptance Criteria:
- [ ] Step 1: Shipping Information
  - [ ] Nama lengkap
  - [ ] Email (untuk guest checkout)
  - [ ] Nomor telepon
  - [ ] Alamat lengkap
  - [ ] Kota & kode pos
  - [ ] Validasi real-time
- [ ] Step 2: Order Summary
  - [ ] List item dengan qty & harga
  - [ ] Subtotal, shipping, total
  - [ ] Notes field (opsional)
- [ ] Step 3: Payment
  - [ ] Midtrans Snap popup
  - [ ] Support: QRIS, GoPay, OVO, Dana, Bank Transfer, CC
  - [ ] Loading state saat generate token
- [ ] Step 4: Confirmation
  - [ ] Order number
  - [ ] Payment status
  - [ ] Ringkasan order
  - [ ] CTA: Continue Shopping / Track Order
```

#### US-007: Guest Checkout
```
Sebagai pembeli,
Saya ingin checkout tanpa registrasi,
Agar saya tidak ribet dengan proses sign up.

Acceptance Criteria:
- [ ] Checkout available tanpa login
- [ ] Email field wajib untuk konfirmasi
- [ ] Order link dikirim via email
- [ ] Optional: "Create account" checkbox di akhir checkout
- [ ] Cart data disimpan di session
```

### 4.4 Epic: Order Management

#### US-008: View Order Status
```
Sebagai pembeli,
Saya ingin melihat status order saya,
Agar saya tahu kapan produk sampai.

Acceptance Criteria:
- [ ] Order confirmation page setelah checkout
- [ ] Order status: Pending -> Paid -> Processing -> Shipped -> Delivered
- [ ] Tracking number (jika shipped)
- [ ] Timeline visual untuk status
- [ ] Email notification tiap status change
```

### 4.5 Epic: Admin Dashboard

#### US-009: View Dashboard Overview
```
Sebagai admin,
Saya ingin melihat overview penjualan,
Agar saya bisa monitor bisnis.

Acceptance Criteria:
- [ ] Total orders (hari ini, minggu ini, bulan ini)
- [ ] Total revenue (hari ini, minggu ini, bulan ini)
- [ ] Total customers
- [ ] Low stock alerts (produk dengan stock < 5)
- [ ] Recent orders table (10 terakhir)
- [ ] Chart: sales 7 hari terakhir
```

#### US-010: Manage Products
```
Sebagai admin,
Saya ingin CRUD produk dengan mudah,
Agar katalog selalu up-to-date.

Acceptance Criteria:
- [ ] Product list dengan search & filter
- [ ] Create product:
  - [ ] Nama, slug (auto-generate), deskripsi (rich text)
  - [ ] Kategori (dropdown)
  - [ ] Harga, harga banding (untuk diskon)
  - [ ] SKU, berat, stok
  - [ ] Upload multiple images (drag & drop)
  - [ ] Set primary image
  - [ ] Variant management (size, color)
  - [ ] Toggle: active, featured
- [ ] Edit product (pre-fill form)
- [ ] Delete product dengan konfirmasi
- [ ] Bulk actions (delete, toggle active)
- [ ] Pagination (20 per halaman)
```

#### US-011: Manage Categories
```
Sebagai admin,
Saya ingin mengelola kategori produk,
Agar produk terorganisir.

Acceptance Criteria:
- [ ] Category list (tree view untuk nested)
- [ ] Create category: nama, slug, deskripsi, image
- [ ] Edit category
- [ ] Delete category (soft delete jika ada produk)
- [ ] Drag & drop reorder (optional)
```

#### US-012: Manage Orders
```
Sebagai admin,
Saya ingin melihat & update status order,
Agar saya bisa proses pengiriman.

Acceptance Criteria:
- [ ] Order list dengan filter (status, date range)
- [ ] Order detail: customer info, items, total, payment
- [ ] Update status dengan dropdown
- [ ] Add tracking number
- [ ] Print invoice (PDF)
- [ ] Send notification email manual
- [ ] Order status history log
```

#### US-013: Configure Settings
```
Sebagai admin,
Saya ingin mengatur konfigurasi toko,
Agar store berjalan sesuai kebutuhan.

Acceptance Criteria:
- [ ] Store info: nama, tagline, logo, favicon
- [ ] Contact info: email, phone, address
- [ ] Social media links
- [ ] Midtrans config: server key, client key, sandbox toggle
- [ ] Shipping settings: default cost, free shipping threshold
- [ ] Email template settings
```


---

## 5. Database Schema

### 5.1 Entity Relationship Diagram

```
users ||--o{ orders : places
users ||--o{ carts : owns
users ||--o{ order_status_histories : changes

categories ||--o{ products : contains
products ||--o{ product_images : has
products ||--o{ product_variants : has
products ||--o{ cart_items : in
products ||--o{ order_items : in

carts ||--o{ cart_items : contains
orders ||--o{ order_items : contains
orders ||--o{ order_status_histories : tracks
```

### 5.2 Detailed Schema

#### Table: users
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| name | VARCHAR(255) | NOT NULL | Nama user |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Email |
| email_verified_at | TIMESTAMP | NULL | Verifikasi email |
| password | VARCHAR(255) | NOT NULL | Hash password |
| phone | VARCHAR(20) | NULL | Nomor telepon |
| address | TEXT | NULL | Alamat lengkap |
| role | ENUM | DEFAULT 'customer' | customer, admin |
| avatar | VARCHAR(255) | NULL | URL avatar |
| remember_token | VARCHAR(100) | NULL | Laravel default |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | |

#### Table: categories
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| name | VARCHAR(255) | NOT NULL | Nama kategori |
| slug | VARCHAR(255) | NOT NULL, UNIQUE | URL-friendly name |
| description | TEXT | NULL | Deskripsi |
| image | VARCHAR(500) | NULL | Cloudinary URL |
| parent_id | BIGINT UNSIGNED | NULL, FK -> categories.id | Kategori parent |
| is_active | BOOLEAN | DEFAULT TRUE | Status aktif |
| sort_order | INT | DEFAULT 0 | Urutan tampil |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: products
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| category_id | BIGINT UNSIGNED | NOT NULL, FK -> categories.id | Kategori |
| name | VARCHAR(255) | NOT NULL | Nama produk |
| slug | VARCHAR(255) | NOT NULL, UNIQUE | URL-friendly |
| description | LONGTEXT | NULL | Deskripsi (HTML) |
| short_description | VARCHAR(500) | NULL | Ringkasan |
| price | DECIMAL(15,2) | NOT NULL | Harga jual |
| compare_at_price | DECIMAL(15,2) | NULL | Harga banding |
| sku | VARCHAR(100) | NOT NULL, UNIQUE | Stock keeping unit |
| stock_quantity | INT UNSIGNED | DEFAULT 0 | Stok tersedia |
| weight | INT UNSIGNED | DEFAULT 0 | Berat (gram) |
| is_active | BOOLEAN | DEFAULT TRUE | Status aktif |
| is_featured | BOOLEAN | DEFAULT FALSE | Produk unggulan |
| meta_title | VARCHAR(255) | NULL | SEO title |
| meta_description | VARCHAR(500) | NULL | SEO description |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: product_images
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| product_id | BIGINT UNSIGNED | NOT NULL, FK -> products.id | Produk |
| image_url | VARCHAR(500) | NOT NULL | Cloudinary URL |
| public_id | VARCHAR(255) | NOT NULL | Cloudinary public ID |
| alt_text | VARCHAR(255) | NULL | Alt text |
| sort_order | INT UNSIGNED | DEFAULT 0 | Urutan |
| is_primary | BOOLEAN | DEFAULT FALSE | Gambar utama |
| created_at | TIMESTAMP | | |

#### Table: product_variants
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| product_id | BIGINT UNSIGNED | NOT NULL, FK -> products.id | Produk |
| variant_name | VARCHAR(255) | NOT NULL | Nama variant |
| sku | VARCHAR(100) | NOT NULL, UNIQUE | SKU variant |
| price_adjustment | DECIMAL(10,2) | DEFAULT 0.00 | Selisih harga |
| stock_quantity | INT UNSIGNED | DEFAULT 0 | Stok variant |
| is_active | BOOLEAN | DEFAULT TRUE | Status |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: variant_attributes
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| name | VARCHAR(100) | NOT NULL | Nama atribut (Color, Size) |
| created_at | TIMESTAMP | | |

#### Table: variant_attribute_values
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| variant_attribute_id | BIGINT UNSIGNED | NOT NULL, FK | Atribut |
| value | VARCHAR(100) | NOT NULL | Nilai (Red, XL) |
| color_code | VARCHAR(7) | NULL | Hex color (untuk swatch) |
| created_at | TIMESTAMP | | |

#### Table: product_variant_combinations
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| product_variant_id | BIGINT UNSIGNED | PK, FK | Variant |
| variant_attribute_value_id | BIGINT UNSIGNED | PK, FK | Nilai atribut |

#### Table: carts
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| user_id | BIGINT UNSIGNED | NULL, FK -> users.id | User (nullable) |
| session_id | VARCHAR(255) | NULL | Session ID untuk guest |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: cart_items
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| cart_id | BIGINT UNSIGNED | NOT NULL, FK -> carts.id | Cart |
| product_id | BIGINT UNSIGNED | NOT NULL, FK -> products.id | Produk |
| product_variant_id | BIGINT UNSIGNED | NULL, FK -> product_variants.id | Variant |
| quantity | INT UNSIGNED | NOT NULL, DEFAULT 1 | Jumlah |
| unit_price | DECIMAL(15,2) | NOT NULL | Harga saat add |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: orders
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| order_number | VARCHAR(50) | NOT NULL, UNIQUE | Nomor order |
| user_id | BIGINT UNSIGNED | NULL, FK -> users.id | User (nullable) |
| guest_email | VARCHAR(255) | NULL | Email guest |
| guest_phone | VARCHAR(20) | NULL | Phone guest |
| guest_name | VARCHAR(255) | NULL | Nama guest |
| shipping_address | JSON | NOT NULL | Alamat pengiriman |
| billing_address | JSON | NULL | Alamat tagihan |
| subtotal | DECIMAL(15,2) | NOT NULL | Subtotal |
| shipping_cost | DECIMAL(10,2) | DEFAULT 0.00 | Ongkir |
| discount_amount | DECIMAL(10,2) | DEFAULT 0.00 | Diskon |
| tax_amount | DECIMAL(10,2) | DEFAULT 0.00 | Pajak |
| total_amount | DECIMAL(15,2) | NOT NULL | Total |
| payment_method | VARCHAR(50) | NULL | Metode pembayaran |
| payment_status | ENUM | DEFAULT 'pending' | pending, paid, failed, expired, refunded |
| midtrans_transaction_id | VARCHAR(255) | NULL | ID transaksi Midtrans |
| midtrans_payment_type | VARCHAR(50) | NULL | Tipe pembayaran Midtrans |
| midtrans_transaction_status | VARCHAR(50) | NULL | Status dari Midtrans |
| order_status | ENUM | DEFAULT 'pending' | pending, processing, shipped, delivered, cancelled, returned |
| tracking_number | VARCHAR(100) | NULL | Nomor resi |
| notes | TEXT | NULL | Catatan |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: order_items
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| order_id | BIGINT UNSIGNED | NOT NULL, FK -> orders.id | Order |
| product_id | BIGINT UNSIGNED | NOT NULL, FK -> products.id | Produk |
| product_variant_id | BIGINT UNSIGNED | NULL | Variant |
| product_name | VARCHAR(255) | NOT NULL | Snapshot nama |
| variant_name | VARCHAR(255) | NULL | Snapshot variant |
| sku | VARCHAR(100) | NOT NULL | Snapshot SKU |
| quantity | INT UNSIGNED | NOT NULL | Jumlah |
| unit_price | DECIMAL(15,2) | NOT NULL | Harga satuan |
| total_price | DECIMAL(15,2) | NOT NULL | Total |
| created_at | TIMESTAMP | | |

#### Table: order_status_histories
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| order_id | BIGINT UNSIGNED | NOT NULL, FK -> orders.id | Order |
| status | VARCHAR(50) | NOT NULL | Status |
| notes | TEXT | NULL | Catatan |
| changed_by | BIGINT UNSIGNED | NULL, FK -> users.id | User yang ubah |
| created_at | TIMESTAMP | | |

#### Table: slides
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| title | VARCHAR(255) | NOT NULL | Judul slide |
| subtitle | VARCHAR(500) | NULL | Subtitle |
| image | VARCHAR(500) | NOT NULL | Cloudinary URL |
| button_text | VARCHAR(100) | NULL | Teks tombol |
| button_link | VARCHAR(500) | NULL | Link tombol |
| sort_order | INT | DEFAULT 0 | Urutan |
| is_active | BOOLEAN | DEFAULT TRUE | Status |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

#### Table: settings
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | Primary key |
| key | VARCHAR(100) | NOT NULL, UNIQUE | Kunci |
| value | TEXT | NULL | Nilai |
| created_at | TIMESTAMP | | |
| updated_at | TIMESTAMP | | |

### 5.3 Indexes
```sql
-- Products
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE FULLTEXT INDEX idx_products_search ON products(name, description);

-- Orders
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(order_status);
CREATE INDEX idx_orders_payment ON orders(payment_status);
CREATE INDEX idx_orders_created ON orders(created_at);

-- Order Items
CREATE INDEX idx_order_items_order ON order_items(order_id);

-- Cart Items
CREATE INDEX idx_cart_items_cart ON cart_items(cart_id);
```


---

## 6. API Specification

### 6.1 Internal API Routes

#### Cart API
```
POST   /api/cart/add          -> Tambah item ke cart
PUT    /api/cart/update/{id}  -> Update qty item
DELETE /api/cart/remove/{id}  -> Hapus item dari cart
GET    /api/cart              -> Get cart items & total
```

#### Checkout API
```
POST   /api/checkout          -> Create order, return snap token
GET    /api/checkout/success  -> Halaman sukses
GET    /api/checkout/pending  -> Halaman pending
GET    /api/checkout/error    -> Halaman error
```

#### Webhook
```
POST   /webhook/midtrans      -> Handle Midtrans notification
```

### 6.2 Midtrans Integration Flow

```
[SEQUENCE DIAGRAM]

Customer          Frontend          Backend           Midtrans
   |                 |                 |                 |
   |--- Click Pay -->|                 |                 |
   |                 |--- POST /api/checkout ----------->|
   |                 |                 |                 |
   |                 |<-- snap_token --|                 |
   |                 |                 |                 |
   |<-- Show Snap ---|                 |                 |
   |                 |                 |                 |
   |--- Complete Payment ------------->|                 |
   |                 |                 |                 |
   |                 |                 |<-- Notification -|
   |                 |                 |                 |
   |                 |                 |-- Verify sig -->|
   |                 |                 |                 |
   |                 |                 |-- Update Order -|
   |                 |                 |                 |
   |<-- Redirect ----|                 |                 |
   |                 |                 |                 |
```

### 6.3 Midtrans Transaction Status Mapping

| Midtrans Status | Order Payment Status | Action |
|-----------------|---------------------|--------|
| capture | paid | Kurangi stock, kirim email |
| settlement | paid | Kurangi stock, kirim email |
| pending | pending | Tunggu pembayaran |
| deny | failed | Allow retry |
| cancel | cancelled | Restore stock |
| expire | expired | Restore stock |
| refund | refunded | Restore stock, kirim email |
| partial_refund | partially_refunded | Adjust stock |

---

## 7. UI/UX Specification

### 7.1 Design System

#### Color Palette
```css
:root {
  /* Primary */
  --color-primary-50:  #EEF2FF;
  --color-primary-100: #E0E7FF;
  --color-primary-200: #C7D2FE;
  --color-primary-300: #A5B4FC;
  --color-primary-400: #818CF8;
  --color-primary-500: #6366F1;  /* Primary */
  --color-primary-600: #4F46E5;
  --color-primary-700: #4338CA;
  --color-primary-800: #3730A3;
  --color-primary-900: #312E81;

  /* Neutral */
  --color-gray-50:  #F9FAFB;
  --color-gray-100: #F3F4F6;
  --color-gray-200: #E5E7EB;
  --color-gray-300: #D1D5DB;
  --color-gray-400: #9CA3AF;
  --color-gray-500: #6B7280;
  --color-gray-600: #4B5563;
  --color-gray-700: #374151;
  --color-gray-800: #1F2937;
  --color-gray-900: #111827;

  /* Semantic */
  --color-success: #10B981;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;
  --color-info: #3B82F6;
}
```

#### Typography
```css
--font-sans: 'Inter', system-ui, sans-serif;
--font-mono: 'JetBrains Mono', monospace;

/* Scale */
--text-xs:   0.75rem;   /* 12px */
--text-sm:   0.875rem;  /* 14px */
--text-base: 1rem;      /* 16px */
--text-lg:   1.125rem;  /* 18px */
--text-xl:   1.25rem;   /* 20px */
--text-2xl:  1.5rem;    /* 24px */
--text-3xl:  1.875rem;  /* 30px */
--text-4xl:  2.25rem;   /* 36px */
--text-5xl:  3rem;      /* 48px */

/* Line height */
--leading-tight: 1.25;
--leading-snug:  1.375;
--leading-normal: 1.5;
--leading-relaxed: 1.625;
```

#### Spacing Scale
```css
--space-0:  0;
--space-1:  0.25rem;   /* 4px */
--space-2:  0.5rem;    /* 8px */
--space-3:  0.75rem;   /* 12px */
--space-4:  1rem;      /* 16px */
--space-5:  1.25rem;   /* 20px */
--space-6:  1.5rem;    /* 24px */
--space-8:  2rem;      /* 32px */
--space-10: 2.5rem;    /* 40px */
--space-12: 3rem;      /* 48px */
--space-16: 4rem;      /* 64px */
--space-20: 5rem;      /* 80px */
--space-24: 6rem;      /* 96px */
```

#### Component Specs

**Buttons**
```
Primary:
- bg: primary-600
- text: white
- padding: py-2.5 px-5
- border-radius: rounded-lg
- hover: bg-primary-700, scale-105
- transition: all 200ms ease
- focus: ring-2 ring-primary-500 ring-offset-2

Secondary:
- bg: white
- border: border-2 border-gray-300
- text: gray-700
- hover: bg-gray-50, border-gray-400

Danger:
- bg: danger-500
- text: white
- hover: bg-danger-600
```

**Cards**
```
Product Card:
- bg: white
- border-radius: rounded-xl
- shadow: shadow-sm
- hover: shadow-md, -translate-y-1
- transition: all 300ms ease
- padding: p-0 (image full width)
- content padding: p-4
```

**Inputs**
```
Text Input:
- bg: white
- border: border border-gray-300
- border-radius: rounded-lg
- padding: py-2.5 px-4
- focus: ring-2 ring-primary-500 border-primary-500
- error: border-danger-500, text-danger-600
- placeholder: text-gray-400
```

### 7.2 Page Layouts

#### Homepage
```
[Navbar: Logo | Search | Cart | Dark Mode Toggle]
[Hero Banner: Full-width carousel, 3-5 slides]
[Featured Categories: 4-6 category cards]
[Featured Products: 6 product cards, horizontal scroll on mobile]
[Promo Banner: Full-width CTA]
[New Arrivals: 4 product cards]
[Newsletter: Email signup form]
[Footer: Links, social, payment methods]
```

#### Product Catalog
```
[Navbar]
[Breadcrumb]
[Page Title + Result Count]
[Sidebar: Filters (category, price, sort)]
[Main: Product Grid (3 cols desktop, 2 tablet, 1 mobile)]
[Pagination]
[Footer]
```

#### Product Detail
```
[Navbar]
[Breadcrumb]
[Grid: Image Gallery (left) | Product Info (right)]
  - Image: Main image + thumbnail list
  - Name, price, rating
  - Variant selector (color swatch, size buttons)
  - Quantity selector
  - Add to Cart button
  - Description tabs
[Related Products: 4 cards]
[Footer]
```

#### Cart Page
```
[Navbar]
[Page Title]
[Grid: Cart Items (left 2/3) | Summary (right 1/3)]
  - Cart Items: Image, name, variant, qty, price, remove
  - Summary: Subtotal, shipping, total, checkout button
[Footer]
```

#### Checkout
```
[Navbar (minimal)]
[Progress Steps: Shipping -> Review -> Payment -> Done]
[Form: Shipping Information]
[Order Summary: Collapsible]
[Payment Button]
[Footer (minimal)]
```

#### Admin Dashboard
```
[Sidebar: Logo, Dashboard, Products, Categories, Orders, Settings]
[Topbar: Search, Notifications, Profile]
[Main Content]
  - Stats Cards (4): Orders, Revenue, Customers, Products
  - Chart: Sales 7 days
  - Recent Orders Table
  - Low Stock Alerts
```

### 7.3 Responsive Breakpoints

| Breakpoint | Width | Layout Changes |
|------------|-------|----------------|
| Mobile | < 640px | Single column, hamburger menu, bottom sheet cart |
| Tablet | 640-1024px | 2-column product grid, sidebar collapsible |
| Desktop | > 1024px | 3-4 column grid, sticky sidebar, hover effects |

### 7.4 Dark Mode

```css
.dark {
  --bg-primary: #0F172A;      /* slate-900 */
  --bg-secondary: #1E293B;     /* slate-800 */
  --bg-card: #334155;          /* slate-700 */
  --text-primary: #F8FAFC;     /* slate-50 */
  --text-secondary: #94A3B8;   /* slate-400 */
  --border-color: #475569;     /* slate-600 */
}
```

Toggle: Sun/Moon icon in navbar. Persist preference in localStorage.

---

## 8. Technical Architecture

### 8.1 System Architecture

```
+-------------------------------------------------------------+
|                        CLIENT                               |
|  +-------------+  +-------------+  +---------------------+  |
|  |   Browser   |  |   Mobile    |  |    Admin Panel      |  |
|  |  (Customer) |  |  (Responsive)|  |   (Laravel Blade)   |  |
|  +------+------+  +------+------+  +----------+----------+  |
+--------|---------------|---------------|---------------------+
         |               |               |
         +---------------+---------------+
                         |
+------------------------v------------------------------------+
|                    APPLICATION LAYER                        |
|  +-----------------------------------------------------+  |
|  |              Laravel 13 + Livewire 3                |  |
|  |  +-------------+  +-------------+  +-------------+  |  |
|  |  |  Controllers|  |   Models    |  |   Policies  |  |  |
|  |  +-------------+  +-------------+  +-------------+  |  |
|  |  +-------------+  +-------------+  +-------------+  |  |
|  |  |   Requests  |  |   Resources |  | Middleware  |  |  |
|  |  |  (Validation)|  |  (API/Blade)|  |  (Auth/Role)|  |  |
|  |  +-------------+  +-------------+  +-------------+  |  |
|  |  +-------------+  +-------------+  +-------------+  |  |
|  |  |   Services  |  |   Events    |  |   Jobs      |  |  |
|  |  |  (Business) |  |  (Listeners)|  |  (Queues)   |  |  |
|  |  +-------------+  +-------------+  +-------------+  |  |
|  +-----------------------------------------------------+  |
+--------------------------+----------------------------------+
                           |
+--------------------------v----------------------------------+
|                    DATA LAYER                               |
|  +-------------+  +-------------+  +---------------------+  |
|  |   MySQL 8   |  |  Cloudinary |  |      Redis          |  |
|  |  (Primary)  |  |  (Images)   |  |  (Cache/Session)    |  |
|  +-------------+  +-------------+  +---------------------+  |
+-------------------------------------------------------------+
                           |
+--------------------------v----------------------------------+
|                 EXTERNAL SERVICES                           |
|  +-------------+  +-------------+  +---------------------+  |
|  |   Midtrans  |  |   Mailtrap  |  |   Laravel Telescope |  |
|  |  (Payment)  |  |  (Dev Email) |  |   (Debug/Monitor)   |  |
|  +-------------+  +-------------+  +---------------------+  |
+-------------------------------------------------------------+
```

### 8.2 Tech Stack Detail

| Layer | Technology | Version | Purpose |
|-------|-----------|---------|---------|
| **OS** | Ubuntu / macOS / Windows WSL | Latest | Development environment |
| **Web Server** | Nginx / Apache | Latest | Production server |
| **PHP** | PHP | 8.3+ | Backend language |
| **Framework** | Laravel | 13.x | Full-stack framework |
| **Frontend** | Livewire | 3.x | Reactive components |
| **Styling** | Tailwind CSS | 3.x+ | Utility CSS |
| **UI Kit** | DaisyUI | 4.x+ | Component library |
| **Icons** | Heroicons | 2.x | SVG icons |
| **Database** | MySQL | 8.0+ | Primary database |
| **Cache** | Redis / File | - | Cache & sessions |
| **Queue** | Database / Redis | - | Async jobs |
| **Search** | MySQL Full-Text | - | Product search (MVP) |
| **Image** | Cloudinary SDK | Latest | Image upload & CDN |
| **Payment** | Midtrans PHP | 2.x | Payment gateway |
| **Email** | Laravel Mail | - | Notifications |
| **Testing** | Pest / PHPUnit | 2.x / 10.x | Unit & feature tests |
| **Debug** | Laravel Debugbar | Latest | Development debugging |
| **Monitor** | Laravel Telescope | Latest | Request monitoring |

### 8.3 Directory Structure

```
ecommerce-vibe/
├── app/
│   ├── Console/
│   │   └── Commands/
│   ├── Events/
│   │   ├── OrderCreated.php
│   │   ├── OrderStatusChanged.php
│   │   └── PaymentReceived.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── HomeController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── ProductController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── OrderController.php
│   │   │       └── SettingController.php
│   │   ├── Middleware/
│   │   │   ├── EnsureUserHasRole.php
│   │   │   └── HandleInertiaRequests.php
│   │   ├── Requests/
│   │   │   ├── CartRequest.php
│   │   │   ├── CheckoutRequest.php
│   │   │   └── Admin/
│   │   │       ├── ProductRequest.php
│   │   │       ├── CategoryRequest.php
│   │   │       └── OrderStatusRequest.php
│   │   └── Resources/
│   │       └── ProductResource.php
│   ├── Jobs/
│   │   ├── SendOrderConfirmationEmail.php
│   │   └── ProcessMidtransWebhook.php
│   ├── Livewire/
│   │   ├── CartIcon.php
│   │   ├── CartItems.php
│   │   ├── ProductFilter.php
│   │   ├── ProductSearch.php
│   │   └── Admin/
│   │       ├── ProductTable.php
│   │       ├── OrderTable.php
│   │       └── DashboardStats.php
│   ├── Mail/
│   │   ├── OrderConfirmation.php
│   │   └── OrderStatusUpdated.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── ProductImage.php
│   │   ├── ProductVariant.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderStatusHistory.php
│   │   ├── Slide.php
│   │   └── Setting.php
│   ├── Policies/
│   │   └── OrderPolicy.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   └── EventServiceProvider.php
│   ├── Services/
│   │   ├── CartService.php
│   │   ├── CheckoutService.php
│   │   ├── MidtransService.php
│   │   └── CloudinaryService.php
│   └── View/
│       └── Components/
│           └── AppLayout.php
├── bootstrap/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cloudinary.php
│   ├── database.php
│   ├── midtrans.php
│   └── ...
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php
│       ├── ProductSeeder.php
│       ├── SlideSeeder.php
│       └── SettingSeeder.php
├── public/
│   ├── images/
│   └── storage/
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   ├── views/
│   │   ├── components/
│   │   │   ├── product-card.blade.php
│   │   │   ├── category-card.blade.php
│   │   │   ├── cart-item.blade.php
│   │   │   ├── order-status-badge.blade.php
│   │   │   └── pagination.blade.php
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── guest.blade.php
│   │   ├── livewire/
│   │   │   ├── cart-icon.blade.php
│   │   │   ├── cart-items.blade.php
│   │   │   ├── product-filter.blade.php
│   │   │   └── admin/
│   │   │       ├── product-table.blade.php
│   │   │       └── order-table.blade.php
│   │   ├── home.blade.php
│   │   ├── products/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── cart.blade.php
│   │   ├── checkout.blade.php
│   │   ├── checkout-success.blade.php
│   │   ├── order-track.blade.php
│   │   └── admin/
│   │       ├── dashboard.blade.php
│   │       ├── products/
│   │       │   ├── index.blade.php
│   │       │   ├── create.blade.php
│   │       │   └── edit.blade.php
│   │       ├── categories/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── orders/
│   │       │   ├── index.blade.php
│   │       │   └── show.blade.php
│   │       └── settings.blade.php
│   └── mails/
│       ├── order-confirmation.blade.php
│       └── order-status.blade.php
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── CartTest.php
│   │   ├── CheckoutTest.php
│   │   └── AdminTest.php
│   └── Unit/
│       ├── CartServiceTest.php
│       └── MidtransServiceTest.php
├── .env
├── .env.example
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
├── tailwind.config.js
└── vite.config.js
```

### 8.4 Service Layer Design

#### CartService
```php
class CartService
{
    public function getCart(): Cart;
    public function addItem(int $productId, ?int $variantId, int $qty): CartItem;
    public function updateItem(int $itemId, int $qty): CartItem;
    public function removeItem(int $itemId): void;
    public function clearCart(): void;
    public function getTotal(): float;
    public function getItemCount(): int;
    public function mergeGuestCart(int $userId): void;
}
```

#### CheckoutService
```php
class CheckoutService
{
    public function createOrder(array $data): Order;
    public function processPayment(Order $order): string; // returns snap_token
    public function handlePaymentNotification(array $payload): void;
    public function confirmOrder(string $orderNumber): Order;
}
```

#### MidtransService
```php
class MidtransService
{
    public function __construct();
    public function createSnapToken(Order $order): string;
    public function verifyNotification(array $payload): bool;
    public function getTransactionStatus(string $orderId): array;
    public function cancelTransaction(string $orderId): bool;
    public function refundTransaction(string $orderId, float $amount): bool;
}
```

#### CloudinaryService
```php
class CloudinaryService
{
    public function upload(UploadedFile $file, string $folder = 'products'): array;
    public function delete(string $publicId): bool;
    public function getUrl(string $publicId, array $options = []): string;
    public function getTransformationUrl(string $publicId, int $width, int $height, string $crop = 'fill'): string;
}
```


---

## 9. Payment Integration

### 9.1 Midtrans Configuration

```php
// config/midtrans.php
return [
    'server_key'    => env('MIDTRANS_SERVER_KEY'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'  => true,
    'is_3ds'        => true,
    'snap_url'      => env('MIDTRANS_IS_PRODUCTION', false) 
                        ? 'https://app.midtrans.com/snap/snap.js'
                        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
```

### 9.2 Environment Variables

```env
# Midtrans
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false

# Cloudinary
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_vibe
DB_USERNAME=root
DB_PASSWORD=

# Mail (Development)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_user
MAIL_PASSWORD=your_mailtrap_pass

# App
APP_NAME="E-Commerce Vibe"
APP_ENV=local
APP_KEY=base64:xxxxxxxx
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### 9.3 Payment Flow Detail

#### Step 1: Initiate Checkout
```php
// CheckoutController@store
public function store(CheckoutRequest $request)
{
    DB::beginTransaction();

    try {
        // 1. Validate cart
        $cart = app(CartService::class)->getCart();
        if ($cart->items->isEmpty()) {
            throw new Exception('Cart is empty');
        }

        // 2. Create order
        $order = app(CheckoutService::class)->createOrder([
            'cart' => $cart,
            'shipping' => $request->validated(),
            'subtotal' => $cart->getSubtotal(),
            'shipping_cost' => $this->calculateShipping($request),
            'total' => $cart->getTotal() + $this->calculateShipping($request),
        ]);

        // 3. Generate Midtrans token
        $snapToken = app(MidtransService::class)->createSnapToken($order);

        // 4. Store token in session
        session(['snap_token' => $snapToken, 'order_id' => $order->id]);

        DB::commit();

        return response()->json([
            'snap_token' => $snapToken,
            'order_number' => $order->order_number,
        ]);

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

#### Step 2: Frontend Payment
```javascript
// resources/js/checkout.js
function payWithSnap(snapToken) {
    window.snap.pay(snapToken, {
        onSuccess: function(result) {
            window.location.href = `/checkout/success?order_id=${result.order_id}`;
        },
        onPending: function(result) {
            window.location.href = `/checkout/pending?order_id=${result.order_id}`;
        },
        onError: function(result) {
            window.location.href = `/checkout/error?order_id=${result.order_id}`;
        },
        onClose: function() {
            alert('Payment popup closed without completing payment');
        }
    });
}
```

#### Step 3: Webhook Handler
```php
// WebhookController@midtrans
public function midtrans(Request $request)
{
    $payload = $request->all();

    // Verify signature
    $signatureKey = $payload['signature_key'] ?? '';
    $expectedSignature = hash('sha512', 
        $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . config('midtrans.server_key')
    );

    if (!hash_equals($expectedSignature, $signatureKey)) {
        return response('Invalid signature', 403);
    }

    // Process notification
    app(CheckoutService::class)->handlePaymentNotification($payload);

    return response('OK', 200);
}
```

#### Step 4: Update Order Status
```php
public function handlePaymentNotification(array $payload): void
{
    $order = Order::where('order_number', $payload['order_id'])->first();

    if (!$order) {
        Log::error('Order not found: ' . $payload['order_id']);
        return;
    }

    $transactionStatus = $payload['transaction_status'];
    $paymentType = $payload['payment_type'];

    // Map Midtrans status to our status
    $statusMap = [
        'capture' => 'paid',
        'settlement' => 'paid',
        'pending' => 'pending',
        'deny' => 'failed',
        'cancel' => 'cancelled',
        'expire' => 'expired',
        'refund' => 'refunded',
    ];

    $newPaymentStatus = $statusMap[$transactionStatus] ?? 'pending';

    // Update order
    $order->update([
        'payment_status' => $newPaymentStatus,
        'midtrans_transaction_id' => $payload['transaction_id'],
        'midtrans_payment_type' => $paymentType,
        'midtrans_transaction_status' => $transactionStatus,
    ]);

    // If paid, update order status and reduce stock
    if ($newPaymentStatus === 'paid') {
        $order->update(['order_status' => 'processing']);
        $this->reduceStock($order);

        // Dispatch events
        event(new PaymentReceived($order));

        // Send email
        SendOrderConfirmationEmail::dispatch($order);
    }

    // Log status change
    OrderStatusHistory::create([
        'order_id' => $order->id,
        'status' => $order->order_status,
        'notes' => "Payment status changed to {$newPaymentStatus} via Midtrans webhook",
    ]);
}
```

---

## 10. Image Management (Cloudinary)

### 10.1 Upload Flow

```php
// AdminProductController@store
public function store(ProductRequest $request)
{
    $product = Product::create($request->validated());

    // Upload images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $result = app(CloudinaryService::class)->upload($image, 'products');

            $product->images()->create([
                'image_url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'alt_text' => $product->name,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }

    return redirect()->route('admin.products.index')
        ->with('success', 'Product created successfully');
}
```

### 10.2 Image Transformations

```php
// Helper untuk generate responsive image URL
function cloudinaryUrl(string $publicId, int $width = null, int $height = null, string $crop = 'fill'): string
{
    $options = [
        'fetch_format' => 'auto',  // Auto WebP/AVIF
        'quality' => 'auto',        // Auto quality
    ];

    if ($width) $options['width'] = $width;
    if ($height) $options['height'] = $height;
    if ($width || $height) $options['crop'] = $crop;

    return Cloudinary::getImage($publicId)->toUrl($options);
}

// Usage in Blade
// <img src="{{ cloudinaryUrl($product->primaryImage->public_id, 400, 400) }}" 
//      srcset="{{ cloudinaryUrl($publicId, 400) }} 400w,
//              {{ cloudinaryUrl($publicId, 800) }} 800w"
//      sizes="(max-width: 640px) 100vw, 400px"
//      alt="{{ $product->name }}">
```

### 10.3 Preset Transformations

| Usage | Width | Height | Crop | Quality |
|-------|-------|--------|------|---------|
| Thumbnail | 300 | 300 | fill | auto |
| Card Image | 400 | 400 | fill | auto |
| Product Detail | 800 | 800 | fit | auto |
| Zoom | 1200 | 1200 | fit | auto |
| Hero Banner | 1920 | 800 | fill | auto |
| Admin Preview | 100 | 100 | fill | auto |

---

## 11. Security Requirements

### 11.1 Authentication & Authorization

```php
// Role-based access
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('/products', AdminProductController::class);
    Route::resource('/categories', AdminCategoryController::class);
    Route::resource('/orders', AdminOrderController::class);
    Route::get('/settings', [SettingController::class, 'index']);
});

// Guest checkout (no auth required)
Route::post('/checkout', [CheckoutController::class, 'store']);
Route::get('/checkout/success', [CheckoutController::class, 'success']);
```

### 11.2 Input Validation

```php
// CheckoutRequest
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'postal_code' => 'required|string|max:10',
        'notes' => 'nullable|string|max:1000',
    ];
}

// ProductRequest (Admin)
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:products,slug,' . $this->product?->id,
        'category_id' => 'required|exists:categories,id',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'compare_at_price' => 'nullable|numeric|min:0|gt:price',
        'sku' => 'required|string|max:100|unique:products,sku,' . $this->product?->id,
        'stock_quantity' => 'required|integer|min:0',
        'weight' => 'required|integer|min:0',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
}
```

### 11.3 CSRF & XSS Protection

- CSRF token di semua form (Laravel default)
- Blade auto-escape output ({{ }})
- Sanitize rich text input (HTML Purifier atau strip tags)
- Content Security Policy headers
- Rate limiting pada API endpoints

### 11.4 File Upload Security

```php
// Validasi file upload
'images.*' => [
    'required',
    'image',
    'mimes:jpeg,png,jpg,webp',
    'max:2048', // 2MB
    function ($attribute, $value, $fail) {
        // Check MIME type
        $mime = $value->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            $fail('Invalid image type');
        }

        // Check dimensions
        $dimensions = getimagesize($value->getPathname());
        if ($dimensions[0] > 5000 || $dimensions[1] > 5000) {
            $fail('Image dimensions too large');
        }
    },
]
```

### 11.5 Payment Security

- Midtrans webhook signature verification (HMAC SHA512)
- Server key disimpan di .env (tidak di-expose ke frontend)
- Snap token hanya valid untuk 1 transaksi
- Order amount validation saat webhook
- Idempotency check untuk prevent double processing

---

## 12. Performance Requirements

### 12.1 Performance Targets

| Metric | Target | Measurement |
|--------|--------|-------------|
| First Contentful Paint (FCP) | < 1.5s | Lighthouse |
| Largest Contentful Paint (LCP) | < 2.5s | Lighthouse |
| Time to Interactive (TTI) | < 3.5s | Lighthouse |
| Cumulative Layout Shift (CLS) | < 0.1 | Lighthouse |
| API Response Time | < 200ms | Laravel Telescope |
| Database Query Time | < 50ms | Laravel Debugbar |
| Page Load (Mobile) | < 3s | Lighthouse Mobile |

### 12.2 Optimization Strategies

#### Database
```php
// Eager loading untuk prevent N+1
$products = Product::with(['category', 'images', 'variants'])
    ->where('is_active', true)
    ->paginate(12);

// Select specific columns
$products = Product::select('id', 'name', 'slug', 'price', 'category_id')
    ->with('category:id,name,slug')
    ->paginate(12);

// Cache categories (rarely change)
$categories = Cache::remember('categories', 3600, function () {
    return Category::where('is_active', true)->get();
});
```

#### Images
```html
<!-- Lazy loading -->
<img loading="lazy" src="..." alt="...">

<!-- Responsive images -->
<img 
    srcset="small.jpg 400w, medium.jpg 800w, large.jpg 1200w"
    sizes="(max-width: 640px) 100vw, 50vw"
    src="fallback.jpg"
    alt="...">

<!-- WebP via Cloudinary -->
<img src="{{ cloudinaryUrl($publicId, 400, 400) }}" alt="...">
```

#### Caching
```php
// Route caching
php artisan route:cache

// Config caching
php artisan config:cache

// View caching
php artisan view:cache

// Query caching
Cache::remember('featured_products', 3600, function () {
    return Product::featured()->limit(6)->get();
});
```

#### Asset Optimization
```javascript
// vite.config.js - Code splitting
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'axios'],
                    admin: ['./resources/js/admin.js'],
                }
            }
        }
    }
});
```


---

## 14. Sprint Planning & Execution

### 14.1 Sprint Overview

| Sprint | Fokus | Durasi | Deliverable |
|--------|-------|--------|-------------|
| **Sprint 0** | Setup & Foundation | 1 hari | Project scaffold, DB, auth, base layout |
| **Sprint 1** | Admin — Dashboard & Settings | 2 hari | Admin dashboard, store settings, admin auth |
| **Sprint 2** | Admin — Category Management | 2 hari | CRUD kategori, nested categories, image upload |
| **Sprint 3** | Admin — Product Management | 3 hari | CRUD produk, variants, images, stock |
| **Sprint 4** | Admin — Order Management | 2 hari | Order list, detail, status update, invoice |
| **Sprint 5** | Customer — Homepage & Catalog | 3 hari | Hero banner, product grid, filter, search |
| **Sprint 6** | Customer — Product Detail | 2 hari | Gallery, variants, add to cart |
| **Sprint 7** | Customer — Cart & Checkout | 3 hari | Cart flow, Midtrans integration, confirmation |
| **Sprint 8** | Polish & Deploy | 2 hari | Dark mode, responsive, testing, deploy |

**Total Timeline: ~20 hari kerja (4 minggu)**

---

### 14.2 Sprint 0: Setup & Foundation

**Goal:** Project scaffold siap untuk development

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 0.1 | Install Laravel 13 + Livewire + Tailwind | 30 min | *"Create Laravel 13 project with Livewire 3 and Tailwind CSS. Setup database connection to MySQL."* |
| 0.2 | Setup authentication (Breeze/Jetstream) | 30 min | *"Install Laravel Breeze with Livewire. Create admin seeder with role-based access."* |
| 0.3 | Create base layouts (app, admin, guest) | 1 jam | *"Create three Blade layouts: app.blade.php (customer), admin.blade.php (sidebar + topbar), guest.blade.php (minimal). Use Tailwind + DaisyUI."* |
| 0.4 | Setup Cloudinary SDK | 30 min | *"Install and configure Cloudinary Laravel SDK. Create CloudinaryService with upload, delete, getUrl methods."* |
| 0.5 | Setup Midtrans SDK | 30 min | *"Install Midtrans PHP SDK. Create config/midtrans.php and MidtransService class."* |
| 0.6 | Create all migrations | 1 jam | *"Generate all database migrations based on this schema: [paste Section 5.2]"* |
| 0.7 | Create all models with relationships | 1 jam | *"Generate Eloquent models with relationships: User, Category, Product, ProductImage, ProductVariant, Cart, CartItem, Order, OrderItem, OrderStatusHistory, Slide, Setting."* |
| 0.8 | Create database seeders | 1 jam | *"Create seeders: 5 categories, 20 products with images, 3 slides, admin user, sample settings."* |

**Definition of Done:**
- [ ] `php artisan migrate:fresh --seed` berjalan tanpa error
- [ ] Login admin berfungsi
- [ ] Base layout admin & customer ter-render
- [ ] Cloudinary upload test berhasil
- [ ] Midtrans config ter-load

---

### 14.3 Sprint 1: Admin — Dashboard & Settings

**Goal:** Admin punya "home base" untuk manage store

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 1.1 | Admin middleware & route protection | 30 min | *"Create EnsureAdmin middleware. Protect all /admin routes. Redirect non-admin to homepage."* |
| 1.2 | Dashboard stats cards | 1 jam | *"Create admin dashboard with 4 stat cards: total orders (today/this week/this month), total revenue, total customers, total products. Use Livewire component."* |
| 1.3 | Recent orders table (dashboard) | 1 jam | *"Add recent orders table to dashboard. Show 10 latest orders with status badge, customer name, total, date."* |
| 1.4 | Low stock alerts widget | 30 min | *"Add low stock alerts widget. Show products with stock < 5. Highlight in red."* |
| 1.5 | Sales chart (7 days) | 1 jam | *"Add sales chart to dashboard using Chart.js or Alpine.js. Show daily revenue for last 7 days."* |
| 1.6 | Store settings form | 1 jam | *"Create settings page with form: store name, tagline, logo upload, contact email, phone, address, social media links. Save to settings table."* |
| 1.7 | Midtrans settings form | 30 min | *"Add Midtrans configuration section to settings: server key, client key, sandbox toggle. Encrypt keys."* |
| 1.8 | Settings seed & default values | 30 min | *"Create SettingSeeder with default store info and Midtrans sandbox config."* |

**Definition of Done:**
- [ ] Admin bisa login dan lihat dashboard
- [ ] Dashboard menampilkan data real dari database
- [ ] Settings bisa di-update dan tersimpan
- [ ] Midtrans config bisa di-toggle sandbox/production

---

### 14.4 Sprint 2: Admin — Category Management

**Goal:** Admin bisa CRUD kategori produk

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 2.1 | Category list page | 1 jam | *"Create admin categories index page. Table with: name, slug, parent, product count, status, actions. Search & pagination."* |
| 2.2 | Create category form | 1 jam | *"Create category form: name (auto-slug), description, image upload (Cloudinary), parent category dropdown, is_active toggle."* |
| 2.3 | Edit category | 30 min | *"Add edit category functionality. Pre-fill form. Handle image replacement (delete old from Cloudinary)."* |
| 2.4 | Delete category | 30 min | *"Add soft delete for categories. Prevent delete if has products. Show confirmation modal."* |
| 2.5 | Nested category support | 1 jam | *"Support nested categories (parent-child). Show tree view in list. Limit depth to 2 levels."* |

**Definition of Done:**
- [ ] CRUD kategori berfungsi 100%
- [ ] Image upload ke Cloudinary
- [ ] Nested categories ter-render dengan benar
- [ ] Tidak bisa delete kategori yang punya produk

---

### 14.5 Sprint 3: Admin — Product Management

**Goal:** Admin bisa CRUD produk lengkap dengan variants & images

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 3.1 | Product list page | 1 jam | *"Create admin products index. Table: image, name, SKU, category, price, stock, status, featured, actions. Filter by category, status. Search by name/SKU."* |
| 3.2 | Create product form (basic) | 1 jam | *"Create product form: name (auto-slug), category, description (rich text/Trix), price, compare_at_price, SKU, stock, weight, is_active, is_featured."* |
| 3.3 | Product image upload (multiple) | 1 jam | *"Add multiple image upload to product form. Drag & drop. Preview thumbnails. Set primary image. Upload to Cloudinary."* |
| 3.4 | Product variant management | 2 jam | *"Add variant management: create variants with name, SKU, price_adjustment, stock. Support variant attributes (Color, Size). Visual swatch for colors."* |
| 3.5 | Edit product | 1 jam | *"Add edit product. Pre-fill all fields. Handle image replacement (add new, remove old from Cloudinary)."* |
| 3.6 | Delete product | 30 min | *"Add soft delete. Delete all associated images from Cloudinary. Show confirmation."* |
| 3.7 | Bulk actions | 30 min | *"Add bulk actions: select multiple products, bulk delete, bulk toggle active/featured."* |

**Definition of Done:**
- [ ] CRUD produk berfungsi 100%
- [ ] Multiple image upload ke Cloudinary
- [ ] Variants dengan attributes berfungsi
- [ ] Bulk actions berfungsi

---

### 14.6 Sprint 4: Admin — Order Management

**Goal:** Admin bisa view & manage orders

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 4.1 | Order list page | 1 jam | *"Create admin orders index. Table: order number, customer, total, payment status, order status, date. Filter by status, date range. Sort by date."* |
| 4.2 | Order detail page | 1 jam | *"Create order detail page: customer info, shipping address, items list with images, payment info, status timeline."* |
| 4.3 | Update order status | 1 jam | *"Add status update dropdown. Options: pending, processing, shipped, delivered, cancelled. Log to order_status_histories."* |
| 4.4 | Add tracking number | 30 min | *"Add tracking number input field. Show in order detail and customer view."* |
| 4.5 | Print invoice | 1 jam | *"Create printable invoice template. Include store info, order details, items, totals. CSS print styles."* |
| 4.6 | Order status history | 30 min | *"Show order status history timeline. Who changed, when, notes."* |

**Definition of Done:**
- [ ] Admin bisa lihat semua orders
- [ ] Status update berfungsi dengan audit trail
- [ ] Invoice bisa di-print
- [ ] Tracking number bisa di-add

---

### 14.7 Sprint 5: Customer — Homepage & Catalog

**Goal:** Customer bisa browse produk

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 5.1 | Hero banner carousel | 1 jam | *"Create homepage hero banner. Auto-slide carousel from slides table. Fade/slide transition. Responsive."* |
| 5.2 | Featured categories section | 30 min | *"Add featured categories section. 4-6 category cards with image, name, product count."* |
| 5.3 | Featured products section | 1 jam | *"Add featured products grid. 6 products with ProductCard component. Show badge (New/Sale)."* |
| 5.4 | Product card component | 1 jam | *"Create reusable ProductCard: image, name, price, compare price (show discount %), rating, quick add to cart. Hover zoom effect."* |
| 5.5 | Product catalog page | 1 jam | *"Create product catalog page. Grid view (default) + list view toggle. Sidebar filters: category checkbox, price range slider. Sort dropdown."* |
| 5.6 | Product search | 1 jam | *"Add search bar to navbar. Search by name/description. Autocomplete suggestions. Search results page with filters."* |
| 5.7 | Newsletter signup | 30 min | *"Add newsletter signup section to homepage. Email input, validation, store to database."* |
| 5.8 | Footer component | 30 min | *"Create footer: store info, quick links, categories, social media, payment methods (Midtrans logos)."* |

**Definition of Done:**
- [ ] Homepage menarik dan responsive
- [ ] Product catalog dengan filter & sort berfungsi
- [ ] Search dengan autocomplete
- [ ] Newsletter signup tersimpan

---

### 14.8 Sprint 6: Customer — Product Detail

**Goal:** Customer bisa lihat detail produk dan add to cart

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 6.1 | Product detail layout | 1 jam | *"Create product detail page. Two-column: image gallery (left), product info (right)."* |
| 6.2 | Image gallery with zoom | 1 jam | *"Add image gallery: main image with zoom on hover, thumbnail list below. Click thumbnail to change main image."* |
| 6.3 | Variant selector | 1 jam | *"Add variant selector: color swatches (visual), size buttons. Update price & stock on selection. Disable out-of-stock variants."* |
| 6.4 | Quantity selector | 30 min | *"Add quantity selector: +/- buttons, min 1, max stock. Real-time validation."* |
| 6.5 | Add to cart | 1 jam | *"Add to cart button. AJAX/Livewire. Show success toast. Update cart count in navbar. Validate stock."* |
| 6.6 | Related products | 30 min | *"Show related products section: same category, exclude current product. 4 cards."* |
| 6.7 | Breadcrumb navigation | 30 min | *"Add breadcrumb: Home > Category > Product Name. Clickable links."* |
| 6.8 | SEO meta tags | 30 min | *"Add dynamic meta tags: title, description, OG image from product primary image."* |

**Definition of Done:**
- [ ] Product detail menampilkan semua info
- [ ] Image gallery dengan zoom
- [ ] Variant selector update price & stock
- [ ] Add to cart berfungsi dengan validasi stock

---

### 14.9 Sprint 7: Customer — Cart & Checkout

**Goal:** Customer bisa checkout dan bayar

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 7.1 | Cart icon in navbar | 30 min | *"Add cart icon to navbar with item count badge. Dropdown mini-cart on hover. Show items, qty, subtotal, checkout button."* |
| 7.2 | Cart page | 1 jam | *"Create full cart page. Table: image, name, variant, qty (+/-), unit price, total, remove button. Summary: subtotal, shipping, total."* |
| 7.3 | Cart operations (update/remove) | 1 jam | *"Add update quantity and remove item. Real-time total update. Stock validation. Empty cart state."* |
| 7.4 | Guest checkout form | 1 jam | *"Create checkout page. Shipping form: name, email, phone, address, city, postal code. Validation. Order summary sidebar."* |
| 7.5 | Midtrans Snap integration | 2 jam | *"Integrate Midtrans Snap. On checkout, create order, generate snap_token, open Snap popup. Handle success/pending/error callbacks."* |
| 7.6 | Webhook handler | 1 jam | *"Create webhook endpoint for Midtrans. Verify signature. Update order status. Reduce stock on payment. Send email."* |
| 7.7 | Order confirmation page | 1 jam | *"Create order confirmation page. Show order number, status, items, total. CTA: continue shopping, track order."* |
| 7.8 | Order tracking page | 1 jam | *"Create order tracking page. Input order number + email. Show order status timeline."* |

**Definition of Done:**
- [ ] Cart berfungsi lengkap (add, update, remove)
- [ ] Checkout flow sampai payment berfungsi
- [ ] Midtrans webhook update status
- [ ] Order confirmation & tracking berfungsi

---

### 14.10 Sprint 8: Polish & Deploy

**Goal:** Production-ready

#### Tasks
| No | Task | Estimasi | Vibe Coding Prompt |
|----|------|----------|-------------------|
| 8.1 | Dark mode toggle | 1 jam | *"Add dark mode toggle to navbar. Persist preference. Use Tailwind dark: classes. Test all pages."* |
| 8.2 | Responsive audit | 1 jam | *"Audit all pages on mobile (375px), tablet (768px), desktop (1440px). Fix layout issues."* |
| 8.3 | Loading states | 1 jam | *"Add loading skeletons for product grid, cart, checkout. Button loading states."* |
| 8.4 | Error handling | 1 jam | *"Add error pages: 404, 500, payment failed. Toast notifications for actions."* |
| 8.5 | Performance optimization | 1 jam | *"Optimize: eager loading, image lazy loading, query caching, route caching. Run Lighthouse audit."* |
| 8.6 | Feature tests | 2 jam | *"Write feature tests: cart, checkout, admin CRUD. Minimum 70% coverage."* |
| 8.7 | Deploy to localhost | 30 min | *"Final test on clean environment. php artisan optimize. Document deploy steps."* |

**Definition of Done:**
- [ ] Dark mode berfungsi di semua halaman
- [ ] Responsive di semua breakpoints
- [ ] Lighthouse score > 90 mobile
- [ ] Feature tests pass
- [ ] Siap deploy ke production

---

### 14.11 Sprint Board (Kanban)

```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│   BACKLOG   │  IN PROGRESS│    REVIEW   │    DONE     │
├─────────────┼─────────────┼─────────────┼─────────────┤
│ Sprint 0    │             │             │             │
│ tasks       │             │             │             │
├─────────────┤             │             │             │
│ Sprint 1    │             │             │             │
│ tasks       │             │             │             │
├─────────────┤             │             │             │
│ Sprint 2    │             │             │             │
│ tasks       │             │             │             │
├─────────────┤             │             │             │
│ ...         │             │             │             │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

---

### 14.12 Daily Standup Template

```
Kemarin:
- [Apa yang dikerjakan kemarin]

Hari ini:
- [Apa yang akan dikerjakan hari ini]

Blocker:
- [Ada kendala? Butuh bantuan?]
```

---

### 14.13 Sprint Review Checklist

Setiap akhir sprint:
- [ ] Demo ke "stakeholder" (diri sendiri/catatan)
- [ ] Review acceptance criteria
- [ ] Update burndown chart
- [ ] Plan next sprint
- [ ] Document lessons learned

---

### 14.14 Definition of Ready (DoR)

Task siap dikerjakan kalau:
- [ ] Acceptance criteria jelas
- [ ] Dependencies sudah selesai
- [ ] Mockup/reference tersedia (jika UI)
- [ ] Database schema sudah final

### 14.15 Definition of Done (DoD)

Task selesai kalau:
- [ ] Kode berfungsi sesuai acceptance criteria
- [ ] Tidak ada console error
- [ ] Responsive di mobile & desktop
- [ ] Code review (self-review)
- [ ] Committed ke Git dengan pesan jelas
