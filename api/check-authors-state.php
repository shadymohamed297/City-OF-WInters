<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $authorsData = [
        [
            'id' => Database::uuid(),
            'slug' => 'ahmed-khaled-tawfik',
            'name_ar' => 'د. أحمد خالد توفيق',
            'name_en' => 'Ahmed Khaled Tawfik',
            'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
            'bio_ar' => 'كاتب وروائي ومترجم وطبيب مصري، يُعد من أبرز وأشهر رواد أدب الرعب والخيال العلمي والإثارة في الوطن العربي ولُقّب بـ "العراب".',
            'bio_en' => 'Prominent Egyptian author, novelist, and pioneer of Arabic horror, thriller and sci-fi literature.',
            'book_slugs' => ['قناع-هابيل', 'اختطاف-غير-متوقع', 'عذاب-سيزيف']
        ],
        [
            'id' => Database::uuid(),
            'slug' => 'khawla-hamdi',
            'name_ar' => 'خولة حمدي',
            'name_en' => 'Khawla Hamdi',
            'photo_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400',
            'bio_ar' => 'كاتبة وأستاذة جامعية تونسية، اشتهرت برواياتها الاجتماعية والإنسانية المؤثرة وحازت أعمالها على شهرة واسعة في العالم العربي.',
            'bio_en' => 'Tunisian author and academic, famous for her emotionally resonant social and romantic novels.',
            'book_slugs' => ['غارثا', 'جراح-لا-تلتئم']
        ],
        [
            'id' => Database::uuid(),
            'slug' => 'marwan-al-roumi',
            'name_ar' => 'د. مروان الرومي',
            'name_en' => 'Dr. Marwan Al-Roumi',
            'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
            'bio_ar' => 'باحث ومؤرخ متخصص في التاريخ البيزنطي وتاريخ الإمبراطوريات القديمة والدراسات الأكاديمية المقارنة.',
            'bio_en' => 'Historian and researcher specializing in Byzantine history and ancient civilizations.',
            'book_slugs' => ['الروم-أصل-الدولة-ومنشأها', 'الروم-الأسرة-المقدونية']
        ]
    ];

    $seeded = [];
    foreach ($authorsData as $ad) {
        $check = $pdo->prepare("SELECT id FROM authors WHERE slug = :slug LIMIT 1");
        $check->execute(['slug' => $ad['slug']]);
        $row = $check->fetch();
        if (!$row) {
            $authorId = $ad['id'];
            $ins = $pdo->prepare("
                INSERT INTO authors (id, slug, name_ar, name_en, photo_url, bio_ar, bio_en, is_active, display_order)
                VALUES (:id, :slug, :name_ar, :name_en, :photo_url, :bio_ar, :bio_en, 1, 0)
            ");
            $ins->execute([
                'id' => $authorId,
                'slug' => $ad['slug'],
                'name_ar' => $ad['name_ar'],
                'name_en' => $ad['name_en'],
                'photo_url' => $ad['photo_url'],
                'bio_ar' => $ad['bio_ar'],
                'bio_en' => $ad['bio_en'],
            ]);
            $seeded[] = $ad['name_ar'];
        } else {
            $authorId = $row['id'];
        }

        // Link books
        foreach ($ad['book_slugs'] as $bslug) {
            $upd = $pdo->prepare("UPDATE products SET author_id = :aid, author_ar = :aname, author_en = :aen WHERE slug = :slug");
            $upd->execute([
                'aid' => $authorId,
                'aname' => $ad['name_ar'],
                'aen' => $ad['name_en'],
                'slug' => $bslug
            ]);
        }
    }

    $allAuthors = $pdo->query("SELECT * FROM authors")->fetchAll();

    Response::ok([
        'message' => 'Seeded successfully',
        'seeded_authors' => $seeded,
        'authors' => $allAuthors
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
