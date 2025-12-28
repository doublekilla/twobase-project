<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Twobase',
            'email' => 'admin@twobase.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08123456789',
        ]);

        // Create Demo User
        User::create([
            'name' => 'John Doe',
            'email' => 'user@twobase.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '08987654321',
            'address' => 'Jl. Contoh No. 123, Jakarta',
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'Kemeja Pria',
                'slug' => 'kemeja-pria',
                'description' => 'Koleksi kemeja pria terbaru dengan berbagai desain modern dan casual',
            ],
            [
                'name' => 'Kaos Pria',
                'slug' => 'kaos-pria',
                'description' => 'Kaos pria berkualitas tinggi dengan bahan nyaman',
            ],
            [
                'name' => 'Celana Pria',
                'slug' => 'celana-pria',
                'description' => 'Celana pria formal dan casual untuk berbagai kesempatan',
            ],
            [
                'name' => 'Dress Wanita',
                'slug' => 'dress-wanita',
                'description' => 'Koleksi dress wanita elegan untuk segala acara',
            ],
            [
                'name' => 'Blouse Wanita',
                'slug' => 'blouse-wanita',
                'description' => 'Blouse wanita trendy dan stylish',
            ],
            [
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'description' => 'Pelengkap gaya Anda dengan aksesoris fashion terkini',
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create Products
        $products = [
            [
                'category_id' => 1,
                'name' => 'Kemeja Formal Slim Fit Navy',
                'description' => 'Kemeja formal slim fit dengan bahan premium cotton. Cocok untuk acara formal dan meeting bisnis. Nyaman dipakai seharian.',
                'price' => 299000,
                'discount_price' => 249000,
                'stock' => 50,
                'size' => 'S,M,L,XL,XXL',
                'color' => 'Navy,White,Black',
                'is_featured' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Kemeja Batik Modern',
                'description' => 'Kemeja batik dengan desain modern dan kontemporer. Perpaduan motif tradisional dengan cutting modern.',
                'price' => 350000,
                'discount_price' => null,
                'stock' => 35,
                'size' => 'M,L,XL',
                'color' => 'Brown,Blue',
                'is_featured' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Kaos Polos Premium Cotton',
                'description' => 'Kaos polos dengan bahan 100% cotton combed 30s. Adem, lembut, dan tidak mudah melar.',
                'price' => 129000,
                'discount_price' => 99000,
                'stock' => 100,
                'size' => 'S,M,L,XL,XXL',
                'color' => 'White,Black,Navy,Grey,Maroon',
                'is_featured' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Kaos Graphic Streetwear',
                'description' => 'Kaos dengan desain graphic streetwear yang keren. Bahan cotton combed dengan sablon berkualitas.',
                'price' => 175000,
                'discount_price' => null,
                'stock' => 45,
                'size' => 'M,L,XL',
                'color' => 'Black,White',
                'is_featured' => false,
            ],
            [
                'category_id' => 3,
                'name' => 'Celana Chino Slim Fit',
                'description' => 'Celana chino slim fit dengan bahan stretch yang nyaman. Cocok untuk casual maupun semi-formal.',
                'price' => 279000,
                'discount_price' => 229000,
                'stock' => 60,
                'size' => '28,29,30,31,32,33,34',
                'color' => 'Khaki,Navy,Black,Grey',
                'is_featured' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Celana Jeans Denim Stretch',
                'description' => 'Celana jeans dengan bahan denim stretch berkualitas. Nyaman dan fleksibel untuk aktivitas sehari-hari.',
                'price' => 325000,
                'discount_price' => null,
                'stock' => 40,
                'size' => '28,30,32,34',
                'color' => 'Blue,Black',
                'is_featured' => false,
            ],
            [
                'category_id' => 4,
                'name' => 'Dress Elegant A-Line',
                'description' => 'Dress A-Line yang elegan dengan cutting yang flattering. Cocok untuk berbagai acara formal.',
                'price' => 450000,
                'discount_price' => 375000,
                'stock' => 25,
                'size' => 'S,M,L,XL',
                'color' => 'Black,Red,Navy',
                'is_featured' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Maxi Dress Floral',
                'description' => 'Maxi dress dengan motif floral yang cantik. Bahan adem dan nyaman untuk cuaca tropis.',
                'price' => 399000,
                'discount_price' => null,
                'stock' => 30,
                'size' => 'S,M,L',
                'color' => 'Pink,Blue',
                'is_featured' => true,
            ],
            [
                'category_id' => 5,
                'name' => 'Blouse Satin Elegant',
                'description' => 'Blouse dengan bahan satin premium yang elegan. Memberikan kesan mewah dan profesional.',
                'price' => 275000,
                'discount_price' => 225000,
                'stock' => 35,
                'size' => 'S,M,L,XL',
                'color' => 'White,Cream,Pink',
                'is_featured' => false,
            ],
            [
                'category_id' => 5,
                'name' => 'Blouse Casual Linen',
                'description' => 'Blouse casual dengan bahan linen yang adem. Perfect untuk daily wear.',
                'price' => 199000,
                'discount_price' => null,
                'stock' => 50,
                'size' => 'S,M,L',
                'color' => 'White,Beige,Light Blue',
                'is_featured' => false,
            ],
            [
                'category_id' => 6,
                'name' => 'Topi Baseball Premium',
                'description' => 'Topi baseball dengan bahan premium dan bordir berkualitas.',
                'price' => 149000,
                'discount_price' => 119000,
                'stock' => 75,
                'size' => 'All Size',
                'color' => 'Black,White,Navy',
                'is_featured' => false,
            ],
            [
                'category_id' => 6,
                'name' => 'Scarf Fashion Silk',
                'description' => 'Scarf fashion dengan bahan silk premium. Multifungsi dan stylish.',
                'price' => 189000,
                'discount_price' => null,
                'stock' => 40,
                'size' => 'All Size',
                'color' => 'Various',
                'is_featured' => true,
            ],
        ];

        foreach ($products as $prod) {
            $product = Product::create(array_merge($prod, [
                'slug' => \Illuminate\Support\Str::slug($prod['name']) . '-' . uniqid(),
                'is_active' => true,
            ]));

            // Create placeholder image reference (we'll use placeholder images)
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'products/placeholder.jpg',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Login: admin@twobase.com / password');
        $this->command->info('User Login: user@twobase.com / password');
    }
}
