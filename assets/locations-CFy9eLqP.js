import { c as React, l as e, o as useStore, p as cn } from "./index-Dmn91ErK.js";

const { useState, useEffect, useMemo } = React;

// Fallback initial data in case API call takes time or network is offline
const INITIAL_DATA = {
  egypt: [
    {
      id: "eg-1",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "القاهرة",
      city_en: "Cairo",
      name_ar: "مكتبة مدبولي",
      name_en: "Madbouly Bookshop",
      branch_name_ar: "فرع وسط البلد",
      branch_name_en: "Downtown Branch",
      address_ar: "6 ميدان طلعت حرب، وسط البلد، القاهرة",
      address_en: "6 Talaat Harb Square, Downtown, Cairo",
      phone: "02-23919888",
      whatsapp: "+201026600868",
      google_maps_url: "https://maps.google.com/?q=Madbouly+Bookshop+Talaat+Harb+Cairo",
      is_main_distributor: 1
    },
    {
      id: "eg-2",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "القاهرة",
      city_en: "Cairo",
      name_ar: "مكتبة تنمية",
      name_en: "Tanmia Bookstore",
      branch_name_ar: "فرع هدى شعراوي",
      branch_name_en: "Hoda Shaarawy Branch",
      address_ar: "19 شارع هدى شعراوي، باب اللوق، وسط البلد، القاهرة",
      address_en: "19 Hoda Shaarawy St, Bab Al-Louq, Downtown, Cairo",
      phone: "01026600868",
      whatsapp: "+201026600868",
      google_maps_url: "https://maps.google.com/?q=Tanmia+Bookstore+Cairo",
      is_main_distributor: 1
    },
    {
      id: "eg-3",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "القاهرة",
      city_en: "Cairo",
      name_ar: "مكتبات ديوان",
      name_en: "Diwan Bookstore",
      branch_name_ar: "فرع الزمالك",
      branch_name_en: "Zamalek Branch",
      address_ar: "159 شارع 26 يوليو، الزمالك، القاهرة",
      address_en: "159 26th of July St, Zamalek, Cairo",
      phone: "02-27362598",
      whatsapp: "+201222407084",
      google_maps_url: "https://maps.google.com/?q=Diwan+Bookstore+Zamalek+Cairo",
      is_main_distributor: 0
    },
    {
      id: "eg-4",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "القاهرة",
      city_en: "Cairo",
      name_ar: "مكتبات الشروق",
      name_en: "Shorouk Bookstore",
      branch_name_ar: "فرع طلعت حرب",
      branch_name_en: "Talaat Harb Branch",
      address_ar: "1 ميدان طلعت حرب، وسط البلد، القاهرة",
      address_en: "1 Talaat Harb Square, Downtown, Cairo",
      phone: "02-23912480",
      whatsapp: "+201001777000",
      google_maps_url: "https://maps.google.com/?q=Shorouk+Bookstore+Talaat+Harb+Cairo",
      is_main_distributor: 1
    },
    {
      id: "eg-5",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "القاهرة",
      city_en: "Cairo",
      name_ar: "مكتبة آفاق",
      name_en: "Afaq Bookstore",
      branch_name_ar: "فرع مدينة نصر",
      branch_name_en: "Nasr City Branch",
      address_ar: "شارع مصطفى النحاس، مدينة نصر، القاهرة",
      address_en: "Mustafa Al-Nahas St, Nasr City, Cairo",
      phone: "01005544332",
      whatsapp: "+201005544332",
      google_maps_url: "https://maps.google.com/?q=Nasr+City+Cairo",
      is_main_distributor: 0
    },
    {
      id: "eg-6",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "القاهرة",
      city_en: "Cairo",
      name_ar: "مكتبة بيت الكتب",
      name_en: "Bait El Kotob",
      branch_name_ar: "فرع التجمع الخامس",
      branch_name_en: "5th Settlement Branch",
      address_ar: "كونكورد بلازا مول، شارع التسعين الجنوبي، التجمع الخامس، القاهرة الجديدة",
      address_en: "Concord Plaza, South 90th St, New Cairo",
      phone: "01123456789",
      whatsapp: "+201123456789",
      google_maps_url: "https://maps.google.com/?q=Concord+Plaza+New+Cairo",
      is_main_distributor: 0
    },
    {
      id: "eg-7",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "الجيزة",
      city_en: "Giza",
      name_ar: "مكتبة الشروق",
      name_en: "Shorouk Bookstore",
      branch_name_ar: "فرع المهندسين",
      branch_name_en: "Mohandessin Branch",
      address_ar: "شارع جزيرة العرب، المهندسين، الجيزة",
      address_en: "Gezirat Al-Arab St, Mohandessin, Giza",
      phone: "02-37604921",
      whatsapp: "+201001777000",
      google_maps_url: "https://maps.google.com/?q=Shorouk+Mohandessin+Giza",
      is_main_distributor: 1
    },
    {
      id: "eg-8",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "الجيزة",
      city_en: "Giza",
      name_ar: "مكتبة ديوان",
      name_en: "Diwan Bookstore",
      branch_name_ar: "فرع الشيخ زايد (مول أركان)",
      branch_name_en: "Arkan Plaza Branch - Sheikh Zayed",
      address_ar: "أركان بلازا، مدخل الشيخ زايد، 6 أكتوبر / الجيزة",
      address_en: "Arkan Plaza, Sheikh Zayed, Giza",
      phone: "02-38507850",
      whatsapp: "+201222407084",
      google_maps_url: "https://maps.google.com/?q=Arkan+Plaza+Sheikh+Zayed",
      is_main_distributor: 0
    },
    {
      id: "eg-9",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "الإسكندرية",
      city_en: "Alexandria",
      name_ar: "مكتبة ديوان",
      name_en: "Diwan Bookstore",
      branch_name_ar: "فرع سان ستيفانو",
      branch_name_en: "San Stefano Branch",
      address_ar: "سان ستيفانو جراند بلازا، طريق الجيش، الإسكندرية",
      address_en: "San Stefano Grand Plaza, Alexandria",
      phone: "03-5818980",
      whatsapp: "+201222407084",
      google_maps_url: "https://maps.google.com/?q=San+Stefano+Alexandria",
      is_main_distributor: 1
    },
    {
      id: "eg-10",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "الإسكندرية",
      city_en: "Alexandria",
      name_ar: "مكتبة منشأة المعارف",
      name_en: "Monshaat Al-Maaref",
      branch_name_ar: "فرع محطة الرمل",
      branch_name_en: "Raml Station Branch",
      address_ar: "شارع سعد زغلول، محطة الرمل، وسط الإسكندرية",
      address_en: "Saad Zaghloul St, Raml Station, Alexandria",
      phone: "03-4876612",
      whatsapp: "+201026600868",
      google_maps_url: "https://maps.google.com/?q=Raml+Station+Alexandria",
      is_main_distributor: 0
    },
    {
      id: "eg-11",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "الدقهلية (المنصورة)",
      city_en: "Mansoura",
      name_ar: "مكتبة الفجر الحديثة",
      name_en: "Al-Fajr Bookshop",
      branch_name_ar: "فرع المشاية السفلية",
      branch_name_en: "Al-Mashaya Branch",
      address_ar: "المشاية السفلية، أمام بوابة الجامعة، المنصورة، الدقهلية",
      address_en: "Lower Mashaya, opposite University gate, Mansoura",
      phone: "050-2234567",
      whatsapp: "+201099887766",
      google_maps_url: "https://maps.google.com/?q=Mansoura+University+Egypt",
      is_main_distributor: 1
    },
    {
      id: "eg-12",
      country_type: "egypt",
      country_ar: "مصر",
      country_en: "Egypt",
      city_ar: "الغربية (طنطا)",
      city_en: "Tanta",
      name_ar: "مكتبة دار الحكمة",
      name_en: "Dar Al-Hikma Bookshop",
      branch_name_ar: "فرع شارع الجيش",
      branch_name_en: "El-Geish St Branch",
      address_ar: "شارع الجيش تقاطع شارع النحاس، طنطا، محافظة الغربية",
      address_en: "El-Geish St intersection with El-Nahas, Tanta",
      phone: "040-3344556",
      whatsapp: "+201011223344",
      google_maps_url: "https://maps.google.com/?q=Tanta+Egypt",
      is_main_distributor: 0
    }
  ],
  international: [
    {
      id: "intl-1",
      country_type: "international",
      country_ar: "المملكة العربية السعودية",
      country_en: "Saudi Arabia",
      city_ar: "الرياض",
      city_en: "Riyadh",
      name_ar: "مكتبة جرير",
      name_en: "Jarir Bookstore",
      branch_name_ar: "فرع طريق الملك فهد",
      branch_name_en: "King Fahd Road Branch",
      address_ar: "طريق الملك فهد، حي العليا، الرياض، المملكة العربية السعودية",
      address_en: "King Fahd Rd, Al-Olaya, Riyadh, Saudi Arabia",
      phone: "+966920000089",
      whatsapp: "+966114626000",
      google_maps_url: "https://maps.google.com/?q=Jarir+Bookstore+Olaya+Riyadh",
      is_main_distributor: 1
    },
    {
      id: "intl-2",
      country_type: "international",
      country_ar: "المملكة العربية السعودية",
      country_en: "Saudi Arabia",
      city_ar: "جدة",
      city_en: "Jeddah",
      name_ar: "مكتبة جرير",
      name_en: "Jarir Bookstore",
      branch_name_ar: "فرع شارع فلسطين",
      branch_name_en: "Palestine St Branch",
      address_ar: "شارع فلسطين، حي الرويس، جدة، المملكة العربية السعودية",
      address_en: "Palestine Street, Al-Ruwais, Jeddah, Saudi Arabia",
      phone: "+966920000089",
      whatsapp: "+966126600000",
      google_maps_url: "https://maps.google.com/?q=Jarir+Bookstore+Palestine+St+Jeddah",
      is_main_distributor: 1
    },
    {
      id: "intl-3",
      country_type: "international",
      country_ar: "الإمارات العربية المتحدة",
      country_en: "United Arab Emirates",
      city_ar: "دبي",
      city_en: "Dubai",
      name_ar: "مكتبة كينوكونيا",
      name_en: "Kinokuniya Bookstore",
      branch_name_ar: "فرع دبي مول",
      branch_name_en: "The Dubai Mall Branch",
      address_ar: "الطابق الثاني، دبي مول، وسط مدينة دبي، الإمارات العربية المتحدة",
      address_en: "Level 2, The Dubai Mall, Downtown Dubai, UAE",
      phone: "+97144340111",
      whatsapp: "+971501234567",
      google_maps_url: "https://maps.google.com/?q=Kinokuniya+Dubai+Mall",
      is_main_distributor: 1
    },
    {
      id: "intl-4",
      country_type: "international",
      country_ar: "الإمارات العربية المتحدة",
      country_en: "United Arab Emirates",
      city_ar: "الشارقة",
      city_en: "Sharjah",
      name_ar: "مكتبة كلمات للنشر والتوزيع",
      name_en: "Kalimat Bookshop",
      branch_name_ar: "فرع القصباء",
      branch_name_en: "Al Qasba Branch",
      address_ar: "قناة القصباء المائية، الشارقة، الإمارات العربية المتحدة",
      address_en: "Al Qasba, Sharjah, United Arab Emirates",
      phone: "+97165560000",
      whatsapp: "+971561234567",
      google_maps_url: "https://maps.google.com/?q=Al+Qasba+Sharjah",
      is_main_distributor: 0
    },
    {
      id: "intl-5",
      country_type: "international",
      country_ar: "الكويت",
      country_en: "Kuwait",
      city_ar: "مدينة الكويت",
      city_en: "Kuwait City",
      name_ar: "مكتبة آفاق",
      name_en: "Afaq Bookstore",
      branch_name_ar: "فرع مجمع التلال",
      branch_name_en: "Al-Tilal Mall Branch",
      address_ar: "مجمع التلال، الشويخ، مدينة الكويت، دولة الكويت",
      address_en: "Al-Tilal Mall, Shuwaikh, Kuwait City",
      phone: "+96522256141",
      whatsapp: "+96598765432",
      google_maps_url: "https://maps.google.com/?q=Al+Tilal+Mall+Kuwait",
      is_main_distributor: 1
    },
    {
      id: "intl-6",
      country_type: "international",
      country_ar: "الأردن",
      country_en: "Jordan",
      city_ar: "عمّان",
      city_en: "Amman",
      name_ar: "مكتبة دار الشروق",
      name_en: "Dar Al-Shorouk Bookshop",
      branch_name_ar: "فرع جبل اللويبدة",
      branch_name_en: "Jabal Al-Weibdeh Branch",
      address_ar: "شارع كلية الشريعة، جبل اللويبدة، عمّان، المملكة الأردنية الهاشمية",
      address_en: "Kulliyat Al-Sharia St, Jabal Al-Weibdeh, Amman, Jordan",
      phone: "+96264618243",
      whatsapp: "+962791234567",
      google_maps_url: "https://maps.google.com/?q=Jabal+Al-Weibdeh+Amman",
      is_main_distributor: 1
    },
    {
      id: "intl-7",
      country_type: "international",
      country_ar: "العراق",
      country_en: "Iraq",
      city_ar: "بغداد",
      city_en: "Baghdad",
      name_ar: "مكتبة دار السطور",
      name_en: "Dar Al-Sutoor Bookshop",
      branch_name_ar: "فرع شارع المتنبي",
      branch_name_en: "Al-Mutanabbi St Branch",
      address_ar: "شارع المتنبي الشهير، قرب مبنى القشلة، بغداد، العراق",
      address_en: "Al-Mutanabbi Street, near Qishla, Baghdad, Iraq",
      phone: "+9647701234567",
      whatsapp: "+9647701234567",
      google_maps_url: "https://maps.google.com/?q=Al-Mutanabbi+Street+Baghdad",
      is_main_distributor: 1
    },
    {
      id: "intl-8",
      country_type: "international",
      country_ar: "معارض الكتاب الدولية",
      country_en: "International Book Fairs",
      city_ar: "كافة العواصم",
      city_en: "All Capitals",
      name_ar: "جناح دار نشر مدينة الأدباء",
      name_en: "Madinat Al-Odabaa Pavilion",
      branch_name_ar: "المعارض الدولية السنوية",
      branch_name_en: "Annual International Fairs",
      address_ar: "معارض الكتاب الدولية (الرياض، الشارقة، أبوظبي، القاهرة، مسقط، الدوحة، والدار البيضاء)",
      address_en: "International Book Fairs (Riyadh, Sharjah, Abu Dhabi, Cairo, Muscat, Doha, Casablanca)",
      phone: "02-339-650-60",
      whatsapp: "+201026600868",
      google_maps_url: "https://maps.google.com/?q=Cairo+International+Book+Fair",
      is_main_distributor: 1
    }
  ]
};

// Inline SVG Icons
function MapPinIcon({ className = "h-4 w-4" }) {
  return e.jsxs("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor",
    strokeWidth: 2,
    children: [
      e.jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", d: "M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" }),
      e.jsx("path", { strokeLinecap: "round", strokeLinejoin: "round", d: "M15 11a3 3 0 11-6 0 3 3 0 016 0z" })
    ]
  });
}

function PhoneIcon({ className = "h-4 w-4" }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor",
    strokeWidth: 2,
    children: e.jsx("path", {
      strokeLinecap: "round",
      strokeLinejoin: "round",
      d: "M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
    })
  });
}

function WhatsAppIcon({ className = "h-4 w-4" }) {
  return e.jsxs("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    viewBox: "0 0 24 24",
    fill: "currentColor",
    children: [
      e.jsx("path", {
        d: "M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"
      })
    ]
  });
}

function DirectionsIcon({ className = "h-4 w-4" }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor",
    strokeWidth: 2,
    children: e.jsx("path", {
      strokeLinecap: "round",
      strokeLinejoin: "round",
      d: "M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
    })
  });
}

function SearchIcon({ className = "h-4 w-4" }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor",
    strokeWidth: 2,
    children: e.jsx("path", {
      strokeLinecap: "round",
      strokeLinejoin: "round",
      d: "M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
    })
  });
}

function BadgeCheckIcon({ className = "h-4 w-4" }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    viewBox: "0 0 20 20",
    fill: "currentColor",
    children: e.jsx("path", {
      fillRule: "evenodd",
      d: "M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z",
      clipRule: "evenodd"
    })
  });
}

function BuildingLibraryIcon({ className = "h-5 w-5" }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className,
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor",
    strokeWidth: 1.8,
    children: e.jsx("path", {
      strokeLinecap: "round",
      strokeLinejoin: "round",
      d: "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
    })
  });
}

export function component() {
  const isAr = useStore((s) => s.locale) === "ar";
  const [activeTab, setActiveTab] = useState("egypt"); // "egypt" | "international"
  const [selectedFilter, setSelectedFilter] = useState("all");
  const [searchQuery, setSearchQuery] = useState("");
  const [data, setData] = useState(INITIAL_DATA);
  const [isLoading, setIsLoading] = useState(false);

  // Fetch branches from backend API on mount
  useEffect(() => {
    let isMounted = true;
    setIsLoading(true);
    fetch("/api/catalog/points-of-sale.php")
      .then((res) => res.json())
      .then((json) => {
        if (isMounted && json && json.ok && json.data) {
          setData({
            egypt: json.data.egypt && json.data.egypt.length > 0 ? json.data.egypt : INITIAL_DATA.egypt,
            international: json.data.international && json.data.international.length > 0 ? json.data.international : INITIAL_DATA.international
          });
        }
      })
      .catch((err) => {
        console.warn("Could not load points-of-sale from API, using fallback data:", err);
      })
      .finally(() => {
        if (isMounted) setIsLoading(false);
      });

    return () => {
      isMounted = false;
    };
  }, []);

  // Reset subfilter when switching tab
  const handleTabChange = (tab) => {
    setActiveTab(tab);
    setSelectedFilter("all");
  };

  const currentBranches = activeTab === "egypt" ? data.egypt : data.international;

  // Extract unique filters (cities for Egypt, countries for International)
  const availableFilters = useMemo(() => {
    const set = new Set();
    currentBranches.forEach((b) => {
      const val = activeTab === "egypt" ? (isAr ? b.city_ar : b.city_en) : (isAr ? b.country_ar : b.country_en);
      if (val) set.add(val);
    });
    return Array.from(set);
  }, [currentBranches, activeTab, isAr]);

  // Filtered branches
  const filteredBranches = useMemo(() => {
    let list = currentBranches;

    if (selectedFilter !== "all") {
      list = list.filter((b) => {
        const val = activeTab === "egypt" ? (isAr ? b.city_ar : b.city_en) : (isAr ? b.country_ar : b.country_en);
        return val === selectedFilter;
      });
    }

    if (searchQuery.trim() !== "") {
      const q = searchQuery.trim().toLowerCase();
      list = list.filter((b) => {
        const hay = [
          b.name_ar,
          b.name_en,
          b.branch_name_ar,
          b.branch_name_en,
          b.address_ar,
          b.address_en,
          b.city_ar,
          b.city_en,
          b.country_ar,
          b.country_en
        ]
          .filter(Boolean)
          .join(" ")
          .toLowerCase();
        return hay.includes(q);
      });
    }

    return list;
  }, [currentBranches, selectedFilter, searchQuery, activeTab, isAr]);

  const egyptCount = data.egypt.length;
  const intlCount = data.international.length;

  return e.jsxs("div", {
    className: "min-h-screen bg-background pb-20",
    children: [
      // Hero Header Banner
      e.jsx("section", {
        className: "relative bg-gradient-to-b from-primary/10 via-primary/5 to-background border-b border-border/60 py-12 md:py-16 overflow-hidden",
        children: e.jsxs("div", {
          className: "container-page relative z-10 text-center max-w-3xl mx-auto",
          children: [
            // Badge
            e.jsxs("div", {
              className: "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 text-primary text-xs md:text-sm font-bold mb-4 shadow-sm",
              children: [
                e.jsx(BuildingLibraryIcon, { className: "h-4 w-4" }),
                e.jsx("span", {
                  children: isAr ? "شبكة منافذ التوزيع المعتمدة" : "Authorized Distribution Network"
                })
              ]
            }),
            // Title
            e.jsx("h1", {
              className: "font-display font-black text-3xl md:text-5xl text-foreground tracking-tight mb-4",
              children: isAr ? "نقاط البيع والمكتبات المعتمدة" : "Points of Sale & Bookstores"
            }),
            // Subtitle
            e.jsx("p", {
              className: "text-muted-foreground text-sm md:text-base leading-relaxed max-w-2xl mx-auto",
              children: isAr
                ? "تعرف على منافذ ومكتبات توفر كتب وروايات وإصدارات دار نشر مدينة الأدباء بالقرب منك، داخل جمهورية مصر العربية ومختلف الدول العربية والدولية."
                : "Explore authorized bookstores and distribution partners offering Madinat Al-Odabaa books and novels in Egypt and worldwide."
            })
          ]
        })
      }),

      // Main Content Container
      e.jsxs("main", {
        className: "container-page pt-8 md:pt-12",
        children: [
          // Segmented Tabs: Egypt vs International
          e.jsxs("div", {
            className: "flex justify-center mb-8",
            children: [
              e.jsxs("div", {
                className: "inline-flex p-1.5 rounded-2xl bg-muted/80 border border-border shadow-inner max-w-md w-full",
                children: [
                  // Egypt Tab
                  e.jsxs("button", {
                    type: "button",
                    onClick: () => handleTabChange("egypt"),
                    className: cn(
                      "flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm md:text-base transition-all duration-200",
                      activeTab === "egypt"
                        ? "bg-background text-primary shadow-md border border-border/50 scale-[1.01]"
                        : "text-muted-foreground hover:text-foreground"
                    ),
                    children: [
                      e.jsx("span", { className: "text-lg md:text-xl", children: "🇪🇬" }),
                      e.jsx("span", { children: isAr ? "داخل مصر (جوه مصر)" : "Inside Egypt" }),
                      e.jsx("span", {
                        className: cn(
                          "text-xs px-2 py-0.5 rounded-full font-bold",
                          activeTab === "egypt" ? "bg-primary/10 text-primary" : "bg-muted text-muted-foreground"
                        ),
                        children: egyptCount
                      })
                    ]
                  }),
                  // International Tab
                  e.jsxs("button", {
                    type: "button",
                    onClick: () => handleTabChange("international"),
                    className: cn(
                      "flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm md:text-base transition-all duration-200",
                      activeTab === "international"
                        ? "bg-background text-primary shadow-md border border-border/50 scale-[1.01]"
                        : "text-muted-foreground hover:text-foreground"
                    ),
                    children: [
                      e.jsx("span", { className: "text-lg md:text-xl", children: "🌍" }),
                      e.jsx("span", { children: isAr ? "خارج مصر (بره مصر)" : "Outside Egypt" }),
                      e.jsx("span", {
                        className: cn(
                          "text-xs px-2 py-0.5 rounded-full font-bold",
                          activeTab === "international" ? "bg-primary/10 text-primary" : "bg-muted text-muted-foreground"
                        ),
                        children: intlCount
                      })
                    ]
                  })
                ]
              })
            ]
          }),

          // Search & Filter Toolbar
          e.jsxs("div", {
            className: "space-y-4 mb-8 bg-card border border-border p-4 md:p-6 rounded-2xl shadow-sm",
            children: [
              // Search Input
              e.jsxs("div", {
                className: "relative w-full",
                children: [
                  e.jsx("div", {
                    className: "absolute inset-y-0 flex items-center pointer-events-none text-muted-foreground", style: isAr ? { right: "1rem" } : { left: "1rem" },
                    children: e.jsx(SearchIcon, { className: "h-5 w-5" })
                  }),
                  e.jsx("input", {
                    type: "search",
                    value: searchQuery,
                    onChange: (ev) => setSearchQuery(ev.target.value),
                    placeholder: isAr
                      ? activeTab === "egypt"
                        ? "ابحث باسم المكتبة، الفرع، أو المنطقة في مصر..."
                        : "ابحث باسم المكتبة، الدولة، أو المدينة..."
                      : "Search by bookstore name, city, or address...",
                    className: "w-full h-12 rounded-xl bg-background border border-input text-foreground placeholder:text-muted-foreground text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 focus:outline-none transition-all", style: isAr ? { paddingRight: "2.75rem", paddingLeft: "2rem" } : { paddingLeft: "2.75rem", paddingRight: "2rem" }
                  }),
                  searchQuery &&
                    e.jsx("button", {
                      type: "button",
                      onClick: () => setSearchQuery(""),
                      className: "absolute inset-y-0 end-0 pe-3 flex items-center text-xs text-muted-foreground hover:text-foreground",
                      children: isAr ? "مسح" : "Clear"
                    })
                ]
              }),

              // Filter Pills
              e.jsxs("div", {
                className: "flex items-center gap-2 overflow-x-auto pb-1 pt-1 scrollbar-none",
                children: [
                  e.jsx("span", {
                    className: "text-xs font-bold text-muted-foreground shrink-0 ps-1",
                    children: isAr ? (activeTab === "egypt" ? "المحافظات:" : "الدول:") : "Filter by:"
                  }),
                  // All Option
                  e.jsx("button", {
                    type: "button",
                    onClick: () => setSelectedFilter("all"),
                    className: cn(
                      "px-3.5 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all",
                      selectedFilter === "all"
                        ? "bg-primary text-primary-foreground shadow-sm"
                        : "bg-muted/70 text-foreground/80 hover:bg-muted"
                    ),
                    children: isAr ? "الكل" : "All"
                  }),
                  // Dynamic Filter Pills
                  availableFilters.map((flt) =>
                    e.jsx(
                      "button",
                      {
                        type: "button",
                        onClick: () => setSelectedFilter(flt),
                        className: cn(
                          "px-3.5 py-1.5 rounded-full text-xs font-bold shrink-0 transition-all",
                          selectedFilter === flt
                            ? "bg-primary text-primary-foreground shadow-sm"
                            : "bg-muted/70 text-foreground/80 hover:bg-muted"
                        ),
                        children: flt
                      },
                      flt
                    )
                  )
                ]
              })
            ]
          }),

          // Results Counter & Active Context
          e.jsxs("div", {
            className: "flex items-center justify-between mb-6 text-sm text-muted-foreground",
            children: [
              e.jsxs("div", {
                className: "flex items-center gap-2 font-medium",
                children: [
                  e.jsx("span", {
                    className: "inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"
                  }),
                  e.jsxs("span", {
                    children: [
                      isAr ? "متاح حالياً:" : "Available now:",
                      " ",
                      e.jsx("strong", { className: "text-foreground font-bold", children: filteredBranches.length }),
                      " ",
                      isAr ? "منفذ ومكتبة بيع" : "locations"
                    ]
                  })
                ]
              }),
              (selectedFilter !== "all" || searchQuery) &&
                e.jsxs("button", {
                  type: "button",
                  onClick: () => {
                    setSelectedFilter("all");
                    setSearchQuery("");
                  },
                  className: "text-xs text-primary font-bold hover:underline",
                  children: [isAr ? "إعادة تعيين الفلاتر" : "Reset filters"]
                })
            ]
          }),

          // Branches Cards Grid
          filteredBranches.length === 0
            ? e.jsxs("div", {
                className: "bg-card border border-border rounded-2xl p-12 text-center max-w-md mx-auto my-8",
                children: [
                  e.jsx("div", {
                    className: "grid h-16 w-16 place-items-center rounded-full bg-muted/80 text-muted-foreground mx-auto mb-4",
                    children: e.jsx(SearchIcon, { className: "h-8 w-8" })
                  }),
                  e.jsx("h3", {
                    className: "font-display font-bold text-lg text-foreground mb-1",
                    children: isAr ? "لم نجد نتائج مطابقة لبحثك" : "No matching locations found"
                  }),
                  e.jsx("p", {
                    className: "text-sm text-muted-foreground mb-6",
                    children: isAr
                      ? "جرب البحث باسم مدينة أخرى أو تصفح كافة منافذ التوزيع المعتمدة."
                      : "Try searching for another city or browse all authorized points of sale."
                  }),
                  e.jsx("button", {
                    type: "button",
                    onClick: () => {
                      setSelectedFilter("all");
                      setSearchQuery("");
                    },
                    className: "h-10 px-5 rounded-xl bg-primary text-primary-foreground font-bold text-sm hover:bg-primary-hover transition-colors shadow-sm",
                    children: isAr ? "عرض كل المنافذ" : "Show all locations"
                  })
                ]
              })
            : e.jsx("div", {
                className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6",
                children: filteredBranches.map((branch) => {
                  const title = isAr ? branch.name_ar : branch.name_en;
                  const branchTitle = isAr ? branch.branch_name_ar : branch.branch_name_en;
                  const address = isAr ? branch.address_ar : branch.address_en;
                  const city = isAr ? branch.city_ar : branch.city_en;
                  const country = isAr ? branch.country_ar : branch.country_en;
                  const rawPhone = branch.phone || "";
                  const cleanPhone = rawPhone.replace(/[^0-9+]/g, "");
                  const rawWa = branch.whatsapp || rawPhone;
                  const cleanWa = rawWa.replace(/[^0-9]/g, "");
                  const mapsUrl = branch.google_maps_url || `https://maps.google.com/?q=${encodeURIComponent(title + " " + address)}`;
                  const waMsg = encodeURIComponent(
                    isAr
                      ? `مرحباً، أود الاستفسار عن توفر كتب وإصدارات دار نشر مدينة الأدباء لديكم في (${title} - ${branchTitle || city})`
                      : `Hello, I would like to inquire about the availability of Madinat Al-Odabaa books at (${title} - ${branchTitle || city})`
                  );

                  return e.jsxs(
                    "article",
                    {
                      className: cn(
                        "group relative bg-card border rounded-2xl p-5 md:p-6 transition-all duration-200 flex flex-col justify-between hover:shadow-lg hover:border-primary/40",
                        branch.is_main_distributor
                          ? "border-amber-400/40 bg-gradient-to-b from-amber-500/[0.03] to-card"
                          : "border-border"
                      ),
                      children: [
                        // Card Top Header
                        e.jsxs("div", {
                          className: "space-y-3",
                          children: [
                            // Badges Row
                            e.jsxs("div", {
                              className: "flex items-center justify-between gap-2 flex-wrap",
                              children: [
                                // City / Location Badge
                                e.jsxs("span", {
                                  className: "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-muted text-foreground text-xs font-semibold",
                                  children: [
                                    e.jsx(MapPinIcon, { className: "h-3.5 w-3.5 text-primary shrink-0" }),
                                    e.jsxs("span", {
                                      children: [
                                        city,
                                        activeTab === "international" && country !== city ? ` • ${country}` : ""
                                      ]
                                    })
                                  ]
                                }),
                                // Main Distributor Badge
                                branch.is_main_distributor
                                  ? e.jsxs("span", {
                                      className: "inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-amber-500/15 text-amber-700 dark:text-amber-300 text-[11px] font-bold border border-amber-500/20",
                                      children: [
                                        e.jsx(BadgeCheckIcon, { className: "h-3.5 w-3.5 text-amber-600 shrink-0" }),
                                        e.jsx("span", { children: isAr ? "موزع رئيسي معتمد" : "Main Distributor" })
                                      ]
                                    })
                                  : null
                              ]
                            }),

                            // Store Name & Branch
                            e.jsxs("div", {
                              className: "pt-1",
                              children: [
                                e.jsx("h2", {
                                  className: "font-display font-extrabold text-xl text-foreground group-hover:text-primary transition-colors",
                                  children: title
                                }),
                                branchTitle &&
                                  e.jsx("p", {
                                    className: "text-xs font-bold text-primary/90 mt-0.5",
                                    children: branchTitle
                                  })
                              ]
                            }),

                            // Address Box
                            e.jsxs("div", {
                              className: "flex items-start gap-2 pt-1 text-sm text-muted-foreground leading-relaxed",
                              children: [
                                e.jsx(MapPinIcon, { className: "h-4 w-4 text-muted-foreground/70 shrink-0 mt-0.5" }),
                                e.jsx("span", { children: address })
                              ]
                            })
                          ]
                        }),

                        // Action Buttons Bar
                        e.jsxs("div", {
                          className: "mt-6 pt-4 border-t border-border/80 flex items-center gap-2 flex-wrap",
                          children: [
                            // WhatsApp Button
                            cleanWa &&
                              e.jsxs("a", {
                                href: `https://wa.me/${cleanWa}?text=${waMsg}`,
                                target: "_blank",
                                rel: "noopener noreferrer",
                                className: "flex-1 inline-flex items-center justify-center gap-1.5 h-10 px-3 rounded-xl font-bold text-xs transition-opacity hover:opacity-90 shadow-sm", style: { backgroundColor: "#25D366", color: "#ffffff" },
                                children: [
                                  e.jsx(WhatsAppIcon, { className: "h-4 w-4" }),
                                  e.jsx("span", { children: isAr ? "واتساب" : "WhatsApp" })
                                ]
                              }),

                            // Phone Call Button
                            cleanPhone &&
                              e.jsxs("a", {
                                href: `tel:${cleanPhone}`,
                                className: "inline-flex items-center justify-center gap-1.5 h-10 px-3 rounded-xl bg-muted hover:bg-primary/10 text-foreground hover:text-primary font-bold text-xs border border-input transition-colors",
                                children: [
                                  e.jsx(PhoneIcon, { className: "h-3.5 w-3.5 shrink-0" }),
                                  e.jsx("span", { children: isAr ? "اتصال" : "Call" })
                                ]
                              }),

                            // Google Maps Button
                            mapsUrl &&
                              e.jsxs("a", {
                                href: mapsUrl,
                                target: "_blank",
                                rel: "noopener noreferrer",
                                className: "inline-flex items-center justify-center gap-1.5 h-10 px-3 rounded-xl bg-muted hover:bg-primary/10 text-foreground hover:text-primary font-bold text-xs border border-input transition-colors",
                                title: isAr ? "الاتجاهات على الخريطة" : "Directions",
                                children: [
                                  e.jsx(DirectionsIcon, { className: "h-3.5 w-3.5 shrink-0" }),
                                  e.jsx("span", { className: "hidden sm:inline", children: isAr ? "الخريطة" : "Map" })
                                ]
                              })
                          ]
                        })
                      ]
                    },
                    branch.id
                  );
                })
              }),

          // CTA Card: Become an Authorized Distributor
          e.jsx("section", {
            className: "mt-16 md:mt-20",
            children: e.jsxs("div", {
              className: "relative rounded-3xl text-white p-8 md:p-12 overflow-hidden shadow-xl", style: { background: "linear-gradient(135deg, #0F2A44 0%, #16426C 50%, #1E5C96 100%)" },
              children: [
                // Background decoration
                e.jsx("div", {
                  className: "absolute -end-10 -bottom-10 w-72 h-72 rounded-full bg-white/5 blur-2xl pointer-events-none"
                }),
                e.jsx("div", {
                  className: "absolute -start-10 -top-10 w-72 h-72 rounded-full bg-amber-400/10 blur-2xl pointer-events-none"
                }),

                // Content
                e.jsxs("div", {
                  className: "relative z-10 max-w-2xl",
                  children: [
                    e.jsxs("span", {
                      className: "inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold mb-3 border border-white/10",
                      children: [
                        e.jsx("span", { children: "🤝" }),
                        e.jsx("span", { children: isAr ? "شراكة وتوزيع" : "Distribution Partnership" })
                      ]
                    }),
                    e.jsx("h2", {
                      className: "font-display font-black text-2xl md:text-4xl leading-tight mb-3 text-white",
                      children: isAr
                        ? "هل تمتلك مكتبة أو نقطة بيع وترغب بتوزيع إصداراتنا؟"
                        : "Do you own a bookstore and want to distribute our publications?"
                    }),
                    e.jsx("p", {
                      className: "text-white/80 text-sm md:text-base leading-relaxed mb-6",
                      children: isAr
                        ? "يسعد دار نشر مدينة الأدباء التعاون مع كبرى المكتبات ومنافذ التوزيع في مصر ومختلف الدول العربية والعالم، مع تقديم أفضل الخصومات والتسهيلات لنشر الثقافة والأدب العربي."
                        : "We are delighted to partner with bookstores and distributors worldwide, offering attractive trade discounts and dedicated publishing house support."
                    }),
                    e.jsxs("div", {
                      className: "flex flex-wrap items-center gap-3",
                      children: [
                        e.jsxs("a", {
                          href: "https://wa.me/201026600868?text=" +
                            encodeURIComponent(
                              isAr
                                ? "مرحباً، أود الاستفسار عن شروط وتفاصيل الانضمام لشبكة موزعي دار نشر مدينة الأدباء"
                                : "Hello, I would like to inquire about joining Madinat Al-Odabaa bookstore distributor network"
                            ),
                          target: "_blank",
                          rel: "noopener noreferrer",
                          className: "inline-flex items-center gap-2 h-12 px-6 rounded-xl font-bold text-sm transition-all shadow-md hover:scale-105 active:scale-95", style: { backgroundColor: "#25D366", color: "#ffffff" },
                          children: [
                            e.jsx(WhatsAppIcon, { className: "h-5 w-5" }),
                            e.jsx("span", {
                              children: isAr ? "تواصل مع إدارة التوزيع عبر واتساب" : "Contact Distribution Team"
                            })
                          ]
                        }),
                        e.jsxs("a", {
                          href: "tel:0233965060",
                          className: "inline-flex items-center gap-2 h-12 px-5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition-colors border border-white/20",
                          children: [
                            e.jsx(PhoneIcon, { className: "h-4 w-4" }),
                            e.jsx("span", { children: "02-339-650-60" })
                          ]
                        })
                      ]
                    })
                  ]
                })
              ]
            })
          })
        ]
      })
    ]
  });
}
