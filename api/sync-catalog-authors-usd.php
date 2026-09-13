<?php

use App\Database;
use App\Response;
use App\Slugify;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();

    // 1. Ensure authors table exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `authors` (
          `id` char(36) NOT NULL,
          `slug` varchar(120) NOT NULL,
          `name_ar` varchar(120) NOT NULL,
          `name_en` varchar(120) NOT NULL,
          `bio_ar` text DEFAULT NULL,
          `bio_en` text DEFAULT NULL,
          `photo_url` text DEFAULT NULL,
          `is_active` tinyint(1) NOT NULL DEFAULT 1,
          `display_order` int(11) NOT NULL DEFAULT 0,
          `created_at` datetime NOT NULL DEFAULT current_timestamp(),
          `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `slug` (`slug`),
          KEY `name_ar` (`name_ar`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Ensure columns exist on products
    $colCheck1 = $pdo->query("
        SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'author_id'
    ")->fetchColumn();
    if (!$colCheck1) {
        $pdo->exec("ALTER TABLE `products` ADD COLUMN `author_id` char(36) DEFAULT NULL AFTER `category_id`, ADD INDEX (`author_id`)");
    }

    $colCheck2 = $pdo->query("
        SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'price_usd'
    ")->fetchColumn();
    if (!$colCheck2) {
        $pdo->exec("ALTER TABLE `products` ADD COLUMN `price_usd` decimal(10,2) DEFAULT NULL AFTER `compare_at_price`, ADD COLUMN `compare_at_price_usd` decimal(10,2) DEFAULT NULL AFTER `price_usd`");
    }

    $authorsList = json_decode(<<<'JSON_AUTHORS'
[
  {
    "name_ar": "رحمة نبيل",
    "name_en": "Rahma Nabil",
    "slug": "rahma-nabil",
    "bio_ar": "كاتبة وروائية مصرية، تميزت بكتابة الروايات الاجتماعية والتشويقية التي حازت على شعبية واسعة لدى القراء.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أية محمد رفعت",
    "name_en": "Aya Mohamed Refaat",
    "slug": "aya-mohamed-refaat",
    "bio_ar": "روائية وكاتبة مصرية بارزة في الأدب الرومانسي والاجتماعي وأدب الإثارة، لها العديد من السلاسل الروائية الشهيرة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "روز أمين",
    "name_en": "Rose Amin",
    "slug": "rose-amin",
    "bio_ar": "كاتبة وروائية متألقة تشتهر برواياتها الرومانسية والاجتماعية الغنية بالعواطف والحبكات الدرامية المؤثرة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "نورهان العشري",
    "name_en": "Nourhan El-Ashry",
    "slug": "nourhan-el-ashry",
    "bio_ar": "كاتبة وروائية ذات أسلوب أدبي مميز، تميزت بإصداراتها في الروايات العاطفية والاجتماعية وسلاسل الروايات الناجحة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "د. عبدالعزيز أبودرهات",
    "name_en": "Dr. Abdelaziz Abu Derhat",
    "slug": "dr-abdelaziz-abu-derhat",
    "bio_ar": "باحث وأكاديمي متخصص في الآثار المصرية والحضارة القديمة واللغة الهيروغليفية والدراسات التاريخية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أسد رستم",
    "name_en": "Asad Rustum",
    "slug": "asad-rustum",
    "bio_ar": "مؤرخ وباحث لبناني كبير، يُعد من كبار مؤرخي الشرق وتاريخ الحضارة الرومانية والبيزنطية وتاريخ الكنيسة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "محمد عبدالقوي",
    "name_en": "Mohamed Abdelkawy",
    "slug": "mohamed-abdelkawy",
    "bio_ar": "كاتب وروائي يتميز بأعماله المشوقة في أدب الغموض والتشويق والألغاز النفسية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "فاطمة طه",
    "name_en": "Fatma Taha",
    "slug": "fatma-taha",
    "bio_ar": "كاتبة وروائية تتميز بأسلوبها السلس وطرحها الاجتماعي الواقعي القريب من وجدان القراء.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "ترجمة: أحمد عبدالستار",
    "name_en": "Ahmed Abdel Sattar (Translator)",
    "slug": "ahmed-abdel-sattar",
    "bio_ar": "مترجم وكاتب متميز نقل العديد من الأعمال العالمية الهامة إلى اللغة العربية بدقة وأسلوب أدبي رفيع.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "عيد عزام",
    "name_en": "Eid Azzam",
    "slug": "eid-azzam",
    "bio_ar": "كاتب وروائي مصري يقدم أعمالاً تتناول قضايا إنسانية واجتماعية ومسارات ملهمة في الحياة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "عمر سراج الدين",
    "name_en": "Omar Serag El-Din",
    "slug": "omar-serag-el-din",
    "bio_ar": "كاتب وروائي مهتم بالعلاقات الإنسانية والوجدانية وتفاصيل المشاعر اليومية للشباب.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "مروة حمدي",
    "name_en": "Marwa Hamdy",
    "slug": "marwa-hamdy",
    "bio_ar": "كاتبة وباحثة متخصصة في تطوير الذات والعلاقات الإنسانية والنجاح الشخصي.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "إلهام عبدالرحمن",
    "name_en": "Ilham Abdelrahman",
    "slug": "ilham-abdelrahman",
    "bio_ar": "كاتبة وروائية متخصصة في أدب الرعب والفانتازيا والغموض المثير.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "محمد عفش",
    "name_en": "Mohamed Afash",
    "slug": "mohamed-afash",
    "bio_ar": "كاتب وروائي يتناول موضوعات ما وراء الطبيعة والتشويق والغرائب.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "فاطمة طه - فاطمة محمد",
    "name_en": "Fatma Taha & Fatma Mohamed",
    "slug": "fatma-taha-fatma-mohamed",
    "bio_ar": "عمل أدبي مشترك يجمع بين قلمي الكاتبتين فاطمة طه وفاطمة محمد في حبكة مشوقة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "مصطفى محمد",
    "name_en": "Mostafa Mohamed",
    "slug": "mostafa-mohamed",
    "bio_ar": "كاتب وروائي يهتم بتسليط الضوء على الفئات المهمشة والقضايا الإنسانية العميقة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "تسنيم هشام",
    "name_en": "Tasneem Hesham",
    "slug": "tasneem-hesham",
    "bio_ar": "روائية شابة تميزت في كتابة الفانتازيا التاريخية والروايات الأسطورية والملحمية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "شهيرة عبدالحميد",
    "name_en": "Shahira Abdelhamid",
    "slug": "shahira-abdelhamid",
    "bio_ar": "كاتبة وروائية تتميز بأعمال الغموض والإثارة والأحداث المشوقة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "سمير جودت",
    "name_en": "Samir Gawdat",
    "slug": "samir-gawdat",
    "bio_ar": "كاتب وباحث في علم النفس وتطوير الشخصية وفهم المشاعر والعلاقات الإنسانية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "مصطفى أيمن",
    "name_en": "Mostafa Ayman",
    "slug": "mostafa-ayman",
    "bio_ar": "كاتب وروائي يقدم أعمالاً مبتكرة في أدب الكوميديا السوداء والتشويق.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "حور حمدان",
    "name_en": "Hoor Hamdan",
    "slug": "hoor-hamdan",
    "bio_ar": "كاتبة وروائية شابة تتناول القضايا الاجتماعية بأسلوب وجداني مشوق.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "فاطمة محمد",
    "name_en": "Fatma Mohamed",
    "slug": "fatma-mohamed",
    "bio_ar": "كاتبة وروائية رومانسية واجتماعية تتميز بحكاياتها الدافئة وأسلوبها الشيق.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "شيماء يسري",
    "name_en": "Shaimaa Yosry",
    "slug": "shaimaa-yosry",
    "bio_ar": "كاتبة وروائية تمزج بين الواقعية السحرية وأصالة البيئة الشعبية المصرية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أية عبدالمنعم سلامه",
    "name_en": "Aya Abdelmonem Salama",
    "slug": "aya-abdelmonem-salama",
    "bio_ar": "كاتبة وروائية شابة تتناول الصراعات النفسية والذكريات الإنسانية بعمق.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "شاهيناز محمد صالح",
    "name_en": "Shahinaz Mohamed Saleh",
    "slug": "shahinaz-mohamed-saleh",
    "bio_ar": "كاتبة وروائية تبحر في قضايا الزمن والذكريات والعلاقات الإنسانية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "يارا علاء",
    "name_en": "Yara Alaa",
    "slug": "yara-alaa",
    "bio_ar": "روائية وكاتبة في أدب الخيال العلمي والفضاء والرحلات الكونية المشوقة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "نور كامل",
    "name_en": "Nour Kamel",
    "slug": "nour-kamel",
    "bio_ar": "كاتب وروائي مهتم بأدب التشويق النفسي وصراع الهويات والأقنعة الإنسانية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "ضحى إمام",
    "name_en": "Doha Emam",
    "slug": "doha-emam",
    "bio_ar": "كاتبة وروائية تميزت بالروايات الدرامية التاريخية والملحمية وقصص الصراع والسلطة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "محمد عبدالمنعم",
    "name_en": "Mohamed Abdelmonem",
    "slug": "mohamed-abdelmonem",
    "bio_ar": "كاتب وباحث يتناول تأثير العصر الرقمي ومنصات التواصل على العلاقات والحب.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "رحاب إبراهيم",
    "name_en": "Rehab Ibrahim",
    "slug": "rehab-ibrahim",
    "bio_ar": "كاتبة وروائية ذات قلم عذب يغوص في مشاعر الأمومة والروابط الأسرية الدافئة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "فاطيما يوسف",
    "name_en": "Fatima Youssef",
    "slug": "fatima-youssef",
    "bio_ar": "كاتبة وروائية تتناول موضوعات الصبر والابتلاء وقوة الإرادة الإنسانية في وجه الألم.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "كمال محمد",
    "name_en": "Kamal Mohamed",
    "slug": "kamal-mohamed",
    "bio_ar": "كاتب وروائي يبحث في الفلسفة والتأملات الوجودية في قالب سردي جميل.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أحمد رضا",
    "name_en": "Ahmed Reda",
    "slug": "ahmed-reda",
    "bio_ar": "كاتب وروائي يقدم أعمالاً تتسم بالإثارة والحركة والبطولة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "ياسين يحيي",
    "name_en": "Yassin Yehia",
    "slug": "yassin-yehia",
    "bio_ar": "كاتب وروائي يتأمل في تناقضات الحياة والهشاشة الإنسانية بأسلوب شاعري.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "إسراء محمد",
    "name_en": "Esraa Mohamed",
    "slug": "esraa-mohamed",
    "bio_ar": "كاتبة وروائية شابة تقدم رؤى استكشافية للبدايات والتحولات في حياة الإنسان.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "فارس عمرو",
    "name_en": "Fares Amr",
    "slug": "fares-amr",
    "bio_ar": "كاتب وباحث في الميثولوجيا والأساطير القديمة وتأثيرها على الثقافات.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "سارة الشحات",
    "name_en": "Sara El-Shahat",
    "slug": "sara-el-shahat",
    "bio_ar": "كاتبة وباحثة في مهارات الحوار والإقناع وتطوير الذات والتفكير النقدي.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "سارة محمد",
    "name_en": "Sara Mohamed",
    "slug": "sara-mohamed",
    "bio_ar": "متخصصة في التسويق والإعلان وصناعة الحملات الإعلانية الناجحة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أحمد حسن",
    "name_en": "Ahmed Hassan",
    "slug": "ahmed-hassan",
    "bio_ar": "خبير واستشاري في فن البيع والتسويق واستراتيجيات إدارة المبيعات وتطوير الأعمال.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أميرة علاء",
    "name_en": "Amira Alaa",
    "slug": "amira-alaa",
    "bio_ar": "كاتبة ومترجمة تهتم بالأدب العالمي والتحليلات النفسية للشخصيات الإنسانية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "رحاب إبراهيم حسن",
    "name_en": "Rehab Ibrahim Hassan",
    "slug": "rehab-ibrahim-hassan",
    "bio_ar": "كاتبة وروائية تتناول قضايا الحب والمشاعر الصامتة في قالب روائي رقيق.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "راويه طه",
    "name_en": "Rawya Taha",
    "slug": "rawya-taha",
    "bio_ar": "كاتبة وروائية متخصصة في أدب الجريمة والتحقيقات والألغاز البوليسية العائلية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أحمد رضا السقا",
    "name_en": "Ahmed Reda El-Sakka",
    "slug": "ahmed-reda-el-sakka",
    "bio_ar": "كاتب ساخر يقدم نصائح وأفكار غير تقليدية في قالب فكاهي ممتع.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "سعاد محمد سلامة",
    "name_en": "Soad Mohamed Salama",
    "slug": "soad-mohamed-salama",
    "bio_ar": "كاتبة وروائية معروفة بكتابة الدراما التاريخية والاجتماعية ذات الحبكة القوية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "ولاء رفعت",
    "name_en": "Walaa Refaat",
    "slug": "walaa-refaat",
    "bio_ar": "روائية متميزة في أدب الغموض والدراما المشوقة والرموز الأدبية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "نجاة محمود",
    "name_en": "Najat Mahmoud",
    "slug": "najat-mahmoud",
    "bio_ar": "كاتبة وروائية تغوص في صراعات القلب والعاطفة والضعف البشري أمام الحب.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "شهد السيد",
    "name_en": "Shahd El-Sayed",
    "slug": "shahd-el-sayed",
    "bio_ar": "كاتبة شابة متخصصة في أدب اليافعين والفانتازيا والعوالم الموازية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "مي المسيري",
    "name_en": "Mai El-Messiri",
    "slug": "mai-el-messiri",
    "bio_ar": "كاتبة وروائية تناقش التعقيدات النفسية والاجتماعية للجرائم والأخطاء الإنسانية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "مروة البطراوي",
    "name_en": "Marwa El-Batrawy",
    "slug": "marwa-el-batrawy",
    "bio_ar": "كاتبة وروائية ذات أسلوب شاعري يتحدث عن الوفاء والمشاعر الصادقة المستمرة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "شروق إبراهيم",
    "name_en": "Shorouk Ibrahim",
    "slug": "shorouk-ibrahim",
    "bio_ar": "كاتبة وروائية تقدم حبكات بوليسية وأسرار غامضة تجذب انتباه القارئ حتى النهاية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "إسلام باكلي",
    "name_en": "Islam Bakli",
    "slug": "islam-bakli",
    "bio_ar": "كاتب وروائي يقدم نصوصاً وجدانية ورسائل إنسانية عميقة حول الصداقة والحياة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "أحمد محمد عبدالستار",
    "name_en": "Ahmed Mohamed Abdel Sattar",
    "slug": "ahmed-mohamed-abdel-sattar",
    "bio_ar": "كاتب وروائي يتناول المعاني الروحية والبحث عن الأمل والنور في ظلمات الحياة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "الاء عبدالحميد",
    "name_en": "Alaa Abdelhamid",
    "slug": "alaa-abdelhamid",
    "bio_ar": "كاتبة وروائية تتميز بأسلوب شاعري وإحساس مرهف في كتابة الروايات الرومانسية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "كريم غباشي",
    "name_en": "Karim Ghobashy",
    "slug": "karim-ghobashy",
    "bio_ar": "كاتب وروائي يمزج بين الفانتازيا والتشويق والألعاب النفسية المثيرة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "معتصم محمد عوض",
    "name_en": "Moatasem Mohamed Awad",
    "slug": "moatasem-mohamed-awad",
    "bio_ar": "كاتب وروائي يقدم حكايات واقعية وتاريخية باسلوب سردي ممتع.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "ليندا أحمد شلهوم",
    "name_en": "Linda Ahmed Shalhoum",
    "slug": "linda-ahmed-shalhoum",
    "bio_ar": "كاتبة وروائية تبدع في نسج الأسرار العائلية والحكايات الاجتماعية المشوقة.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "إبراهيم محمد",
    "name_en": "Ibrahim Mohamed",
    "slug": "ibrahim-mohamed",
    "bio_ar": "كاتب يقدم معالجة جريئة ومثيرة للجدل لقضايا واقعية في قالب سردي مميز.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "ترجمة تركية/ ماريهان الطحان",
    "name_en": "Marihan El-Tahhan (Turkish Translator)",
    "slug": "marihan-el-tahhan",
    "bio_ar": "مترجمة متخصصة في نقل الأدب التركي والروايات التركية المشوقة إلى القارئ العربي.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "رامي الجمل",
    "name_en": "Ramy El-Gamal",
    "slug": "ramy-el-gamal",
    "bio_ar": "باحث وداعية إسلامي متخصص في علوم القرآن والتفسير والتدبر وتيسير فهم كتاب الله.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  },
  {
    "name_ar": "د/محمد عبدالرحيم",
    "name_en": "Dr. Mohamed Abdelrahim",
    "slug": "dr-mohamed-abdelrahim",
    "bio_ar": "طبيب واستشاري متخصص في الصحة النفسية وتعديل السلوك وعلاقة المشاعر بالعادات اليومية.",
    "bio_en": "Author at Madinat Al-Odabaa Publishing House. Notable works published and distributed across the Arab world."
  }
]
JSON_AUTHORS, true);

    $booksList = json_decode(<<<'JSON_BOOKS'
[
  {
    "author": "رحمة نبيل",
    "book": "قصر الخواجة",
    "price_usd": 9.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "أبناء الخضري",
    "price_usd": 12.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "طالووس - غارثا الجزء الثاني",
    "price_usd": 7.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "صرخات أنثى 2 - أربع كتب",
    "price_usd": 45.0
  },
  {
    "author": "روز أمين",
    "book": "أودعتك نبضي \"قطار العودة\"",
    "price_usd": 9.0
  },
  {
    "author": "روز أمين",
    "book": "معزوفة الحب والوجع (قلوب حائرة \"الجزء الثاني\") - 4 كتب",
    "price_usd": 45.0
  },
  {
    "author": "نورهان العشري",
    "book": "ذاكرة الرماد \"حيث ينفُث الرماد عطرًا\"",
    "price_usd": 12.0
  },
  {
    "author": "نورهان العشري",
    "book": "ما خبأته السماء \"تردى في العشق قتيلًا\"",
    "price_usd": 9.0
  },
  {
    "author": "محمد عبدالقوي",
    "book": "الهاتف",
    "price_usd": 7.0
  },
  {
    "author": "عيد عزام",
    "book": "طريق بلا عودة \"حين يصبح الأمل أخطر من الموت\"",
    "price_usd": 7.0
  },
  {
    "author": "مصطفى أيمن",
    "book": "طريقة طبخ جثة بالمنزل",
    "price_usd": 7.0
  },
  {
    "author": "حور حمدان",
    "book": "أهداني ضحايا",
    "price_usd": 7.0
  },
  {
    "author": "فاطمة طه",
    "book": "بين أروقة المورستان",
    "price_usd": 8.0
  },
  {
    "author": "فاطمة محمد",
    "book": "ورطة في جزيرة الحب",
    "price_usd": 6.0
  },
  {
    "author": "شيماء يسري",
    "book": "من بولاق إلى ارتكيدوس",
    "price_usd": 6.0
  },
  {
    "author": "عمر سراج الدين",
    "book": "تمام.. بس مش تمام",
    "price_usd": 6.0
  },
  {
    "author": "أية عبدالمنعم سلامه",
    "book": "ذاكرة مشروخة",
    "price_usd": 5.0
  },
  {
    "author": "شاهيناز محمد صالح",
    "book": "على قارعة الزمان",
    "price_usd": 9.0
  },
  {
    "author": "يارا علاء",
    "book": "وسيلة عبر السديم",
    "price_usd": 6.0
  },
  {
    "author": "نور كامل",
    "book": "قناع هابيل",
    "price_usd": 7.0
  },
  {
    "author": "مروة حمدي",
    "book": "ما بعد السقوط \"موسم النجاح\"",
    "price_usd": 5.0
  },
  {
    "author": "إلهام عبدالرحمن",
    "book": "أنثى أنتقم لها الجن",
    "price_usd": 7.0
  },
  {
    "author": "ضحى إمام",
    "book": "الملكة الأسيرة",
    "price_usd": 9.0
  },
  {
    "author": "محمد عبدالمنعم",
    "book": "الحب في عصر السوشيال ميديا",
    "price_usd": 9.0
  },
  {
    "author": "رحاب إبراهيم",
    "book": "إليك صغيرتي نورس",
    "price_usd": 8.0
  },
  {
    "author": "فاطيما يوسف",
    "book": "عطر على صراط الألم \"ولكن المسك فاح\"",
    "price_usd": 8.0
  },
  {
    "author": "محمد عفش",
    "book": "لم تكتب بعد",
    "price_usd": 5.0
  },
  {
    "author": "محمد عفش",
    "book": "ما وراء الكوابيس",
    "price_usd": 5.0
  },
  {
    "author": "كمال محمد",
    "book": "حين ينكسر الضوء",
    "price_usd": 6.0
  },
  {
    "author": "أحمد رضا",
    "book": "النسر",
    "price_usd": 8.0
  },
  {
    "author": "ياسين يحيي",
    "book": "لم تقتلني العقارب، قتلتني فراشة",
    "price_usd": 5.0
  },
  {
    "author": "إسراء محمد",
    "book": "سِفر البداية",
    "price_usd": 6.0
  },
  {
    "author": "د. عبدالعزيز أبودرهات",
    "book": "الأخذ بالثأر والإنتقام في مصر القديمة",
    "price_usd": 11.0
  },
  {
    "author": "د. عبدالعزيز أبودرهات",
    "book": "الدلالات السياقية لبعض العلامات الهيروغليفية",
    "price_usd": 8.0
  },
  {
    "author": "د. عبدالعزيز أبودرهات",
    "book": "الضوضاء في مصر القديمة",
    "price_usd": 7.0
  },
  {
    "author": "د. عبدالعزيز أبودرهات",
    "book": "دفشو - مدينة منسية في غرب الدلتا في ضوء الاكتشافات الأقرية الحديثة",
    "price_usd": 10.0
  },
  {
    "author": "د. عبدالعزيز أبودرهات",
    "book": "خلود الاسم وأهميته في الحضارة المصرية القديمة",
    "price_usd": 10.0
  },
  {
    "author": "ترجمة: أحمد عبدالستار",
    "book": "عزيزي ثيو \"الجزء الأول\"",
    "price_usd": 7.0
  },
  {
    "author": "ترجمة: أحمد عبدالستار",
    "book": "عزيزي ثيو \"الجزء الثاني\"",
    "price_usd": 7.0
  },
  {
    "author": "ترجمة: أحمد عبدالستار",
    "book": "عزيزي ثيو \"الجزء الثالث\"",
    "price_usd": 7.0
  },
  {
    "author": "فارس عمرو",
    "book": "الإله العظيم بان",
    "price_usd": 6.0
  },
  {
    "author": "سارة الشحات",
    "book": "فن أن تكون دائمًا على صواب",
    "price_usd": 4.0
  },
  {
    "author": "سارة محمد",
    "book": "الإعلان الناجح - كيف يمكن تحقيقه",
    "price_usd": 10.0
  },
  {
    "author": "أحمد حسن",
    "book": "فن البيع وإدارة المبيعات",
    "price_usd": 10.0
  },
  {
    "author": "أميرة علاء",
    "book": "أربعة وعشرون ساعة من حياة امرأة",
    "price_usd": 4.0
  },
  {
    "author": "أسد رستم",
    "book": "الروم (أصل الدولة ومنشأها)",
    "price_usd": 7.0
  },
  {
    "author": "أسد رستم",
    "book": "حضارة الروم (بين تطور النظم والانتعاش والتوطيد والاستقرار)",
    "price_usd": 7.0
  },
  {
    "author": "أسد رستم",
    "book": "الروم (بين الرحلة الأولى والتفكك والانهيار)",
    "price_usd": 7.0
  },
  {
    "author": "أسد رستم",
    "book": "الروم (الأسرة المقدونية)",
    "price_usd": 7.0
  },
  {
    "author": "أسد رستم",
    "book": "الروم (اليقظة الأخيرة واخفاقها والنهاية)",
    "price_usd": 7.0
  },
  {
    "author": "رحمة نبيل",
    "book": "حارة الجزار",
    "price_usd": 7.0
  },
  {
    "author": "رحمة نبيل",
    "book": "قرية الشيخ",
    "price_usd": 7.0
  },
  {
    "author": "رحمة نبيل",
    "book": "مدرسة ميلر",
    "price_usd": 6.0
  },
  {
    "author": "رحمة نبيل",
    "book": "خرونس",
    "price_usd": 7.0
  },
  {
    "author": "رحمة نبيل",
    "book": "براءة ظُلمه \"الجزء الأول\"",
    "price_usd": 7.0
  },
  {
    "author": "رحمة نبيل",
    "book": "براءة ظُلمه \"الجزء الثاني\"",
    "price_usd": 7.0
  },
  {
    "author": "رحمة نبيل",
    "book": "لعنة الفراعنة",
    "price_usd": 10.0
  },
  {
    "author": "رحمة نبيل",
    "book": "لأجلك عزيزي البائس",
    "price_usd": 2.0
  },
  {
    "author": "رحمة نبيل",
    "book": "مملكة سفيد \"خمس كتب\"",
    "price_usd": 55.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "غارثا",
    "price_usd": 7.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "مزرعة بني يعقوب",
    "price_usd": 6.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "مافيا الحي الشعبي \"كتابين معًا\"",
    "price_usd": 20.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "صرخات أنثى 1 - ثلاث كتب",
    "price_usd": 36.0
  },
  {
    "author": "روز أمين",
    "book": "جراح لا تلتئم",
    "price_usd": 9.0
  },
  {
    "author": "روز أمين",
    "book": "أقفال موصدة",
    "price_usd": 7.0
  },
  {
    "author": "روز أمين",
    "book": "قلبي بنارها مغرم \"أربع كتب معًا\"",
    "price_usd": 32.0
  },
  {
    "author": "روز أمين",
    "book": "قلوب حائرة \"ثلاث كتب معًا\"",
    "price_usd": 36.0
  },
  {
    "author": "نورهان العشري",
    "book": "ميثاق الحب والياقوت",
    "price_usd": 9.0
  },
  {
    "author": "نورهان العشري",
    "book": "في قبضة الأقدار \"كتابين معًا\"",
    "price_usd": 20.0
  },
  {
    "author": "محمد عبدالقوي",
    "book": "ما لا يراه البعض",
    "price_usd": 8.0
  },
  {
    "author": "محمد عبدالقوي",
    "book": "عذاب سيزيف",
    "price_usd": 6.0
  },
  {
    "author": "محمد عبدالقوي",
    "book": "سر الألة الكاتبة",
    "price_usd": 6.0
  },
  {
    "author": "فاطمة طه",
    "book": "على طريقة السيدة لواحظ",
    "price_usd": 7.0
  },
  {
    "author": "فاطمة طه",
    "book": "أحلام العصر",
    "price_usd": 5.0
  },
  {
    "author": "فاطمة طه",
    "book": "أوكازيون البهجة",
    "price_usd": 6.0
  },
  {
    "author": "فاطمة طه - فاطمة محمد",
    "book": "نقوش ملعونة",
    "price_usd": 4.0
  },
  {
    "author": "مصطفى محمد",
    "book": "مهمشون \"الجزء الأول\"",
    "price_usd": 5.0
  },
  {
    "author": "مصطفى محمد",
    "book": "مهمشون \"الجزء الثاني\"",
    "price_usd": 5.0
  },
  {
    "author": "تسنيم هشام",
    "book": "دماء هجينة",
    "price_usd": 6.0
  },
  {
    "author": "تسنيم هشام",
    "book": "ملحمة ماريسا",
    "price_usd": 5.0
  },
  {
    "author": "رحاب إبراهيم حسن",
    "book": "لها بلقبك شيء",
    "price_usd": 5.0
  },
  {
    "author": "عيد عزام",
    "book": "رجال صدقوا",
    "price_usd": 5.0
  },
  {
    "author": "راويه طه",
    "book": "جريمة عائلية",
    "price_usd": 5.0
  },
  {
    "author": "شهيرة عبدالحميد",
    "book": "تل حوين",
    "price_usd": 6.0
  },
  {
    "author": "أحمد رضا السقا",
    "book": "دليلك إلى 15 خطة في منتهى الغباء",
    "price_usd": 5.0
  },
  {
    "author": "سعاد محمد سلامة",
    "book": "ملك سليمان",
    "price_usd": 7.0
  },
  {
    "author": "ولاء رفعت",
    "book": "الأوركيد الأسود",
    "price_usd": 8.0
  },
  {
    "author": "نجاة محمود",
    "book": "والقلب في الهوى عليل",
    "price_usd": 4.0
  },
  {
    "author": "شهد السيد",
    "book": "مابل والبوابة النجمية",
    "price_usd": 4.0
  },
  {
    "author": "مي المسيري",
    "book": "جناة لكن أبرياء",
    "price_usd": 5.0
  },
  {
    "author": "سمير جودت",
    "book": "في بيتنا طبيب نفسي",
    "price_usd": 7.0
  },
  {
    "author": "سمير جودت",
    "book": "كتالوج المشاعر",
    "price_usd": 7.0
  },
  {
    "author": "مروة البطراوي",
    "book": "بِكَ بقيتُ",
    "price_usd": 5.0
  },
  {
    "author": "شروق إبراهيم",
    "book": "السر الثاني عشر",
    "price_usd": 4.0
  },
  {
    "author": "إسلام باكلي",
    "book": "يوسف يا صديق",
    "price_usd": 9.0
  },
  {
    "author": "عمر سراج الدين",
    "book": "ندبات القلب قد لا تُشفى",
    "price_usd": 5.0
  },
  {
    "author": "أحمد محمد عبدالستار",
    "book": "كفوف النور",
    "price_usd": 6.0
  },
  {
    "author": "الاء عبدالحميد",
    "book": "معزوفة خاصة",
    "price_usd": 7.0
  },
  {
    "author": "كريم غباشي",
    "book": "كيد ساحر",
    "price_usd": 5.0
  },
  {
    "author": "مروة حمدي",
    "book": "كتاب العلاقات",
    "price_usd": 4.0
  },
  {
    "author": "معتصم محمد عوض",
    "book": "حكايات العميد",
    "price_usd": 5.0
  },
  {
    "author": "ليندا أحمد شلهوم",
    "book": "سر فلوريت",
    "price_usd": 7.0
  },
  {
    "author": "إلهام عبدالرحمن",
    "book": "اختطاف غير متوقع",
    "price_usd": 9.0
  },
  {
    "author": "إبراهيم محمد",
    "book": "خمر ونساء ولب أبيض",
    "price_usd": 5.0
  },
  {
    "author": "رحمة نبيل",
    "book": "الطعنة السابعة",
    "price_usd": 6.0
  },
  {
    "author": "روز أمين",
    "book": "زيف الحرير",
    "price_usd": 6.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "عتاولة المخابرات",
    "price_usd": 8.0
  },
  {
    "author": "أية محمد رفعت",
    "book": "مغوار إيكاتا",
    "price_usd": 9.0
  },
  {
    "author": "نورهان العشري",
    "book": "لؤلؤ مكنون",
    "price_usd": 8.0
  },
  {
    "author": "نورهان العشري",
    "book": "بين غياهب الأقدار - الكتاب الأول",
    "price_usd": 12.0
  },
  {
    "author": "نورهان العشري",
    "book": "بين غياهب الأقدار - الكتاب الثاني",
    "price_usd": 12.0
  },
  {
    "author": "شهيرة عبدالحميد",
    "book": "عين عفريت",
    "price_usd": 5.0
  },
  {
    "author": "فاطمة طه - فاطمة محمد",
    "book": "الزيتونة الأخيرة",
    "price_usd": 6.0
  },
  {
    "author": "ترجمة تركية/ ماريهان الطحان",
    "book": "القصر الملعون",
    "price_usd": 5.0
  },
  {
    "author": "رامي الجمل",
    "book": "ما لا يسع حامل القرآن جهله",
    "price_usd": 9.0
  },
  {
    "author": "د/محمد عبدالرحيم",
    "book": "توقف عن أكل مشاعرك",
    "price_usd": 9.0
  }
]
JSON_BOOKS, true);

    // 2. Upsert all 60 Authors
    $authorMap = []; // name_ar => author record
    $authorsCreated = 0;
    $authorsUpdated = 0;

    foreach ($authorsList as $a) {
        $check = $pdo->prepare("SELECT id FROM authors WHERE name_ar = :name_ar OR slug = :slug LIMIT 1");
        $check->execute(['name_ar' => $a['name_ar'], 'slug' => $a['slug']]);
        $existing = $check->fetch();

        if ($existing) {
            $authorId = $existing['id'];
            $upd = $pdo->prepare("
                UPDATE authors 
                SET name_ar = :name_ar, name_en = :name_en, slug = :slug, bio_ar = :bio_ar, bio_en = :bio_en, is_active = 1
                WHERE id = :id
            ");
            $upd->execute([
                'name_ar' => $a['name_ar'],
                'name_en' => $a['name_en'],
                'slug' => $a['slug'],
                'bio_ar' => $a['bio_ar'],
                'bio_en' => $a['bio_en'],
                'id' => $authorId,
            ]);
            $authorsUpdated++;
        } else {
            $authorId = Database::uuid();
            $ins = $pdo->prepare("
                INSERT INTO authors (id, slug, name_ar, name_en, bio_ar, bio_en, is_active, display_order)
                VALUES (:id, :slug, :name_ar, :name_en, :bio_ar, :bio_en, 1, 0)
            ");
            $ins->execute([
                'id' => $authorId,
                'slug' => $a['slug'],
                'name_ar' => $a['name_ar'],
                'name_en' => $a['name_en'],
                'bio_ar' => $a['bio_ar'],
                'bio_en' => $a['bio_en'],
            ]);
            $authorsCreated++;
        }
        $authorMap[$a['name_ar']] = [
            'id' => $authorId,
            'name_ar' => $a['name_ar'],
            'name_en' => $a['name_en'],
            'slug' => $a['slug'],
        ];
    }

    // Helper: Normalize Arabic string for robust matching
    $cleanAr = function (?string $text): string {
        if (!$text) return '';
        $t = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text); // diacritics
        $t = preg_replace('/["״'«»()—\-\–\.\،\:\/]/u', ' ', $t);
        $t = preg_replace('/[إأآاٱ]/u', 'ا', $t);
        $t = preg_replace('/ة/u', 'ه', $t);
        $t = preg_replace('/ى/u', 'ي', $t);
        $t = preg_replace('/\s+/u', ' ', $t);
        return trim($t);
    };

    $titleAliases = [
        $cleanAr('صرخات أنثى 1 - ثلاث كتب') => $cleanAr('صرخات أنثى "ثلاث كتب" الأجزاء الأولى'),
        $cleanAr('صرخات أنثى 2 - أربع كتب') => $cleanAr('صرخات أنثى "أربع كتب" الأجزاء الأخيرة'),
        $cleanAr('معزوفة الحب والوجع (قلوب حائرة "الجزء الثاني") - 4 كتب') => $cleanAr('معزوفة الحب والوجع "أربع كتب معًا"'),
        $cleanAr('قلوب حائرة "ثلاث كتب معًا"') => $cleanAr('قلوب حائرة "ثلاث كتب"'),
        $cleanAr('لها بلقبك شيء') => $cleanAr('لها بقلبك شيء'),
    ];

    // 3. Fetch all current products
    $allProducts = $pdo->query("SELECT id, slug, title_ar, title_en, price, compare_at_price, category_id FROM products")->fetchAll();

    $productsUpdated = 0;
    $productsInserted = 0;
    $matchedIds = [];

    // Get fallback categories
    $catNovels = $pdo->query("SELECT id FROM categories WHERE slug = 'novels' LIMIT 1")->fetchColumn();
    $catSelfHelp = $pdo->query("SELECT id FROM categories WHERE slug = 'self-help' LIMIT 1")->fetchColumn();
    $catScience = $pdo->query("SELECT id FROM categories WHERE slug = 'science' LIMIT 1")->fetchColumn();

    foreach ($booksList as $b) {
        $authorName = $b['author'];
        $authorInfo = $authorMap[$authorName] ?? null;
        if (!$authorInfo) continue;

        $rawBook = $b['book'];
        $cleanBook = $cleanAr($rawBook);
        $targetClean = $titleAliases[$cleanBook] ?? $cleanBook;
        $usdPrice = (float) $b['price_usd'];

        // Find match in products
        $matchedProduct = null;
        foreach ($allProducts as $p) {
            if (isset($matchedIds[$p['id']])) continue;
            $pClean = $cleanAr($p['title_ar']);
            $pSlugClean = $cleanAr(str_replace('-', ' ', $p['slug']));

            if ($targetClean === $pClean || 
                $targetClean === $pSlugClean ||
                ($cleanBook !== '' && str_contains($pClean, $cleanBook)) ||
                ($targetClean !== '' && str_contains($pClean, $targetClean)) ||
                ($pClean !== '' && str_contains($targetClean, $pClean))) {
                $matchedProduct = $p;
                break;
            }
        }

        if ($matchedProduct) {
            $pid = $matchedProduct['id'];
            $matchedIds[$pid] = true;

            // Calculate compare_at_price_usd if product has compare_at_price
            $compareUsd = null;
            if (!empty($matchedProduct['compare_at_price']) && !empty($matchedProduct['price']) && $matchedProduct['compare_at_price'] > $matchedProduct['price']) {
                $ratio = (float) $matchedProduct['compare_at_price'] / (float) $matchedProduct['price'];
                $compareUsd = round($usdPrice * $ratio, 2);
            }

            $upd = $pdo->prepare("
                UPDATE products 
                SET author_id = :aid,
                    author_ar = :aname_ar,
                    author_en = :aname_en,
                    price_usd = :price_usd,
                    compare_at_price_usd = :compare_usd
                WHERE id = :id
            ");
            $upd->execute([
                'aid' => $authorInfo['id'],
                'aname_ar' => $authorInfo['name_ar'],
                'aname_en' => $authorInfo['name_en'],
                'price_usd' => $usdPrice,
                'compare_usd' => $compareUsd,
                'id' => $pid,
            ]);
            $productsUpdated++;
        } else {
            // Insert new product
            $newId = Database::uuid();
            $matchedIds[$newId] = true;

            // Clean slug
            $cleanSlug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $rawBook);
            $cleanSlug = preg_replace('/\s+/u', '-', trim($cleanSlug));
            if ($cleanSlug === '') {
                $cleanSlug = 'book-' . substr($newId, 0, 8);
            }

            // Assign category
            $catId = $catNovels;
            if (str_contains($rawBook, 'مشاعرك') || str_contains($rawBook, 'العلاقات') || str_contains($rawBook, 'طبيب نفسي')) {
                $catId = $catSelfHelp ?: $catNovels;
            } elseif (str_contains($rawBook, 'القرآن')) {
                $catId = $catScience ?: $catNovels;
            }

            $egpPrice = round($usdPrice * 25.0, 0);

            $ins = $pdo->prepare("
                INSERT INTO products (
                    id, slug, title_ar, title_en, author_id, author_ar, author_en,
                    category_id, price, price_usd, compare_at_price_usd, stock, unlimited_stock,
                    publisher_ar, publisher_en, is_active, display_order
                ) VALUES (
                    :id, :slug, :title_ar, :title_en, :author_id, :author_ar, :author_en,
                    :category_id, :price, :price_usd, NULL, 50, 1,
                    'دار نشر مدينة الأدباء', 'Madinat Al-Odabaa Publishing', 1, 0
                )
            ");
            $ins->execute([
                'id' => $newId,
                'slug' => $cleanSlug,
                'title_ar' => $rawBook,
                'title_en' => $rawBook,
                'author_id' => $authorInfo['id'],
                'author_ar' => $authorInfo['name_ar'],
                'author_en' => $authorInfo['name_en'],
                'category_id' => $catId,
                'price' => $egpPrice,
                'price_usd' => $usdPrice,
            ]);
            $productsInserted++;
        }
    }

    // 4. Remove stale test authors that have 0 books
    $del = $pdo->prepare("
        DELETE FROM authors 
        WHERE id NOT IN (SELECT DISTINCT author_id FROM products WHERE author_id IS NOT NULL AND author_id != '')
          AND slug IN ('ahmed-khaled-tawfik', 'khawla-hamdi', 'marwan-al-roumi')
    ");
    $del->execute();

    // 5. Query verification stats
    $totalAuthorsInDb = (int) $pdo->query("SELECT COUNT(*) FROM authors WHERE is_active = 1")->fetchColumn();
    $totalProductsInDb = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    $productsWithAuthor = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE author_id IS NOT NULL AND is_active = 1")->fetchColumn();
    $productsWithUsd = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE price_usd IS NOT NULL AND is_active = 1")->fetchColumn();

    // Sample authors with their books count
    $sampleAuthors = $pdo->query("
        SELECT a.name_ar, a.slug, COUNT(p.id) as books_count, ROUND(AVG(p.price_usd), 2) as avg_usd
        FROM authors a
        JOIN products p ON p.author_id = a.id
        GROUP BY a.id, a.name_ar, a.slug
        ORDER BY books_count DESC
        LIMIT 10
    ")->fetchAll();

    Response::ok([
        'message' => 'Catalog, authors, and USD pricing synced successfully!',
        'authors_created' => $authorsCreated,
        'authors_updated' => $authorsUpdated,
        'products_updated' => $productsUpdated,
        'products_inserted' => $productsInserted,
        'total_active_authors' => $totalAuthorsInDb,
        'total_active_products' => $totalProductsInDb,
        'products_with_author_id' => $productsWithAuthor,
        'products_with_price_usd' => $productsWithUsd,
        'top_authors' => $sampleAuthors,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}
