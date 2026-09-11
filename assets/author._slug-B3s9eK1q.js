import{k as B,o as T,c as p,l as e,au as W,L as $,x as P,a0 as S,W as b,ar as D,as as Q,at as E,ak as K}from"./index-Dmn91ErK-v3.js";
import{P as ProductCard}from"./ProductCard-Cqya0jKe.js";
import{P as ProductSkeletons}from"./skeletons-Bg0_N_m0.js";

const FeatherIcon = B("Feather", [
  ["path", { d: "M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z", key: "f-1" }],
  ["line", { x1: "16", y1: "8", x2: "2", y2: "22", key: "f-2" }],
  ["line", { x1: "17.5", y1: "15", x2: "9", y2: "15", key: "f-3" }]
]);

const BookIcon = B("BookOpen", [
  ["path", { d: "M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z", key: "b-1" }],
  ["path", { d: "M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z", key: "b-2" }]
]);

const LinkIcon = B("Link2", [
  ["path", { d: "M9 17H7A5 5 0 0 1 7 7h2", key: "l-1" }],
  ["path", { d: "M15 7h2a5 5 0 1 1 0 10h-2", key: "l-2" }],
  ["line", { x1: "8", x2: "16", y1: "12", y2: "12", key: "l-3" }]
]);

async function fetchAuthorProfile(slug) {
  return S.get("/api/catalog/author.php?slug=" + encodeURIComponent(slug));
}

function AuthorShareBar({ url, title, isAr }) {
  const [copied, setCopied] = p.useState(false);
  const u = encodeURIComponent(url);
  const t = encodeURIComponent(title);

  const channels = [
    { key: "facebook", label: "Facebook", Icon: D, href: `https://www.facebook.com/sharer/sharer.php?u=${u}`, className: "bg-[#1877F2] hover:bg-[#0d6ae0] text-white" },
    { key: "twitter", label: "X", Icon: Q, href: `https://twitter.com/intent/tweet?url=${u}&text=${t}`, className: "bg-black hover:bg-neutral-800 text-white" },
    { key: "whatsapp", label: "WhatsApp", Icon: E, href: `https://wa.me/?text=${t}%20${u}`, className: "bg-[#25D366] hover:bg-[#1ebe57] text-white" }
  ];

  const handleCopy = async () => {
    try {
      await navigator.clipboard.writeText(url);
      setCopied(true);
      b.success(isAr ? "تم نسخ رابط البروفايل" : "Profile link copied");
      setTimeout(() => setCopied(false), 2000);
    } catch {
      b.error(isAr ? "تعذر النسخ" : "Copy failed");
    }
  };

  return e.jsxs("div", {
    className: "flex items-center gap-2 flex-wrap",
    children: [
      e.jsx("span", {
        className: "text-xs text-muted-foreground font-semibold",
        children: isAr ? "مشاركة:" : "Share:"
      }),
      channels.map(item =>
        e.jsx("a", {
          href: item.href,
          target: "_blank",
          rel: "noopener noreferrer",
          "aria-label": `Share on ${item.label}`,
          className: `grid h-8 w-8 place-items-center rounded-full shadow-xs transition-transform hover:scale-110 ${item.className}`,
          children: e.jsx(item.Icon, { className: "h-3.5 w-3.5" })
        }, item.key)
      ),
      e.jsx("button", {
        onClick: handleCopy,
        "aria-label": "Copy link",
        className: "grid h-8 w-8 place-items-center rounded-full bg-muted hover:bg-muted/80 text-foreground transition-transform hover:scale-110",
        children: copied ? e.jsx(K, { className: "h-3.5 w-3.5 text-success" }) : e.jsx(LinkIcon, { className: "h-3.5 w-3.5" })
      })
    ]
  });
}

function AuthorProfilePage() {
  const params = W.useParams();
  const rawSlug = params?.slug;
  const slug = typeof rawSlug === "string" ? rawSlug : (rawSlug?.data?.slug || rawSlug?.slug || (typeof rawSlug?.data === "string" ? rawSlug.data : "") || "");
  const locale = T(s => s.locale);
  const isAr = locale === "ar";

  const { data, isLoading } = P({
    queryKey: ["author", slug],
    queryFn: () => fetchAuthorProfile(slug),
    enabled: Boolean(slug),
    staleTime: 300 * 1000
  });

  if (isLoading) {
    return e.jsxs("div", {
      className: "container-page py-12",
      children: [
        e.jsx("div", { className: "h-48 rounded-3xl bg-muted/30 animate-pulse mb-8" }),
        e.jsx(ProductSkeletons, { count: 4 })
      ]
    });
  }

  const author = data?.author;
  const products = data?.products ?? [];

  if (!author) {
    return e.jsxs("div", {
      className: "container-page py-20 text-center space-y-4",
      children: [
        e.jsx("div", { className: "text-6xl mb-2", children: "🪶" }),
        e.jsx("h1", {
          className: "font-display font-black text-2xl md:text-3xl text-foreground",
          children: isAr ? "عذراً، لم نتمكن من العثور على الكاتب" : "Author not found"
        }),
        e.jsx("p", {
          className: "text-muted-foreground text-sm max-w-md mx-auto",
          children: isAr ? "قد يكون الرابط غير صحيح أو تم نقل صفحة الكاتب." : "The link might be incorrect or the profile has been moved."
        }),
        e.jsx("div", {
          className: "pt-4",
          children: e.jsx($, {
            to: "/shop",
            className: "inline-flex h-11 px-6 rounded-xl bg-primary text-primary-foreground font-bold hover:bg-primary-hover shadow-sm items-center justify-center",
            children: isAr ? "تصفح كل الكتب" : "Browse all books"
          })
        })
      ]
    });
  }

  const authorName = isAr ? author.name_ar : (author.name_en || author.name_ar);
  const authorBio = isAr ? author.bio_ar : (author.bio_en || author.bio_ar);
  const initialLetter = (author.name_ar || author.name_en || "ك")[0];
  const profileUrl = typeof window !== "undefined" ? window.location.href : `https://www.madinatalodabaa.com/author/${author.slug}`;

  return e.jsxs("div", {
    className: "container-page py-8 md:py-12 space-y-10",
    children: [
      // Breadcrumbs
      e.jsxs("nav", {
        className: "flex items-center gap-2 text-xs text-muted-foreground",
        children: [
          e.jsx($, { to: "/", className: "hover:text-primary transition-colors", children: isAr ? "الرئيسية" : "Home" }),
          e.jsx("span", { children: "/" }),
          e.jsx($, { to: "/shop", className: "hover:text-primary transition-colors", children: isAr ? "المتجر" : "Store" }),
          e.jsx("span", { children: "/" }),
          e.jsx("span", { className: "text-foreground font-semibold", children: authorName })
        ]
      }),

      // Hero Profile Card
      e.jsx("div", {
        className: "relative overflow-hidden rounded-3xl border border-border/80 bg-gradient-to-br from-card via-card to-primary/5 p-6 md:p-10 shadow-card-soft",
        children: e.jsxs("div", {
          className: "flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8 text-center md:text-start",
          children: [
            // Author Photo / Monogram
            e.jsx("div", {
              className: "relative h-32 w-32 md:h-40 md:w-40 shrink-0 rounded-3xl border-4 border-background shadow-lg overflow-hidden bg-primary/10 flex items-center justify-center",
              children: author.photo_url
                ? e.jsx("img", {
                    src: author.photo_url,
                    alt: authorName,
                    className: "h-full w-full object-cover",
                    onError: t => { t.target.style.display = "none"; }
                  })
                : e.jsx("span", {
                    className: "font-display font-black text-5xl md:text-6xl text-primary",
                    children: initialLetter
                  })
            }),

            // Details
            e.jsxs("div", {
              className: "flex-1 space-y-3",
              children: [
                e.jsxs("div", {
                  className: "flex flex-wrap items-center justify-center md:justify-start gap-2",
                  children: [
                    e.jsxs("span", {
                      className: "px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold inline-flex items-center gap-1.5",
                      children: [
                        e.jsx(FeatherIcon, { className: "h-3.5 w-3.5" }),
                        isAr ? "مؤلف وكاتب" : "Author"
                      ]
                    }),
                    products.length > 0 && e.jsxs("span", {
                      className: "px-3 py-1 rounded-full bg-muted text-muted-foreground text-xs font-semibold inline-flex items-center gap-1.5",
                      children: [
                        e.jsx(BookIcon, { className: "h-3.5 w-3.5" }),
                        `${products.length} ${isAr ? "كتاب متاح" : "books available"}`
                      ]
                    })
                  ]
                }),

                e.jsx("h1", {
                  className: "font-display font-black text-3xl md:text-5xl text-foreground tracking-tight leading-tight",
                  children: authorName
                }),

                author.name_en && author.name_en !== author.name_ar && e.jsx("p", {
                  className: "text-base text-muted-foreground font-medium",
                  children: author.name_en
                }),

                // Bio
                authorBio && e.jsx("div", {
                  className: "pt-2",
                  children: e.jsx("p", {
                    className: "text-foreground/80 leading-relaxed text-sm md:text-base max-w-3xl",
                    children: authorBio
                  })
                }),

                // Share Buttons
                e.jsx("div", {
                  className: "pt-3 flex justify-center md:justify-start",
                  children: e.jsx(AuthorShareBar, {
                    url: profileUrl,
                    title: `${authorName} | دار نشر مدينة الأدباء`,
                    isAr: isAr
                  })
                })
              ]
            })
          ]
        })
      }),

      // Author Works / Books Section
      e.jsxs("section", {
        className: "space-y-6 pt-4",
        children: [
          e.jsxs("div", {
            className: "flex items-center justify-between border-b border-border pb-4",
            children: [
              e.jsxs("div", {
                children: [
                  e.jsx("h2", {
                    className: "font-display font-extrabold text-2xl md:text-3xl text-foreground",
                    children: isAr ? `مؤلفات وروايات ${authorName}` : `Works by ${authorName}`
                  }),
                  e.jsx("p", {
                    className: "text-xs md:text-sm text-muted-foreground mt-1",
                    children: isAr
                      ? `استكشف جميع الإصدارات والكتب المتوفرة للطلب الفوري`
                      : `Explore all titles available for immediate delivery`
                  })
                ]
              }),
              e.jsxs("span", {
                className: "text-sm text-muted-foreground font-bold hidden sm:inline",
                children: [products.length, " ", isAr ? "كتاب" : "titles"]
              })
            ]
          }),

          // Empty books state
          products.length === 0 && e.jsxs("div", {
            className: "text-center py-16 bg-card rounded-2xl border border-border p-6 space-y-3",
            children: [
              e.jsx("div", { className: "text-4xl", children: "📚" }),
              e.jsx("h3", {
                className: "font-bold text-lg text-foreground",
                children: isAr ? "لا توجد كتب متاحة حالياً لهذا الكاتب" : "No books available currently"
              }),
              e.jsx("p", {
                className: "text-sm text-muted-foreground max-w-sm mx-auto",
                children: isAr ? "تابعنا لمعرفة أحدث الإصدارات القادمة لهذا المؤلف." : "Stay tuned for upcoming releases."
              })
            ]
          }),

          // Grid of Books
          products.length > 0 && e.jsx("div", {
            className: "grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-6",
            children: products.map((book, idx) =>
              e.jsx(ProductCard, {
                product: book,
                index: idx
              }, book.id)
            )
          })
        ]
      })
    ]
  });
}

export { AuthorProfilePage as component };
