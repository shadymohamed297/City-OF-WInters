import{k as B,o as U,u as Y,x as Z,c as a,l as e,ai as q,aj as V,a6 as ee,ak as se,W as v,O as SearchIcon}from"./index-Dmn91ErK.js";
import{u as G}from"./useMutation-B6M8l77A.js";
import{ga as getAuthors,pa as postAuthor,da as delAuthor}from"./admin-Bj8jRXbE.js";
import{L as O}from"./loader-circle-CH_wIG6b.js";
import{S as R}from"./square-pen-CEgPWieQ.js";
import{I as ImageUpload}from"./ImageUpload--RCD9_7F.js";

const FeatherIcon = B("Feather", [
  ["path", { d: "M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z", key: "feather-1" }],
  ["line", { x1: "16", y1: "8", x2: "2", y2: "22", key: "feather-2" }],
  ["line", { x1: "17.5", y1: "15", x2: "9", y2: "15", key: "feather-3" }]
]);

const BookIcon = B("BookOpen", [
  ["path", { d: "M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z", key: "book-1" }],
  ["path", { d: "M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z", key: "book-2" }]
]);

const ExternalLinkIcon = B("ExternalLink", [
  ["path", { d: "M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6", key: "ext-1" }],
  ["polyline", { points: "15 3 21 3 21 9", key: "ext-2" }],
  ["line", { x1: "10", y1: "14", x2: "21", y2: "3", key: "ext-3" }]
]);

function AdminAuthors() {
  const isAr = U(s => s.locale) === "ar";
  const qc = Y();

  // Fetch authors + all books
  const query = Z({
    queryKey: ["admin", "authors"],
    queryFn: () => getAuthors(true)
  });

  const authors = query.data?.authors ?? [];
  const allBooks = query.data?.all_books ?? [];

  const [search, setSearch] = a.useState("");
  const [modalOpen, setModalOpen] = a.useState(!1);
  const [editingAuthor, setEditingAuthor] = a.useState(null);
  const [deleteConfirmId, setDeleteConfirmId] = a.useState(null);

  // Form states
  const [nameAr, setNameAr] = a.useState("");
  const [nameEn, setNameEn] = a.useState("");
  const [slug, setSlug] = a.useState("");
  const [photoUrl, setPhotoUrl] = a.useState("");
  const [bioAr, setBioAr] = a.useState("");
  const [bioEn, setBioEn] = a.useState("");
  const [displayOrder, setDisplayOrder] = a.useState(0);
  const [isActive, setIsActive] = a.useState(!0);

  // Books linking state
  const [selectedBookIds, setSelectedBookIds] = a.useState([]);
  const [initialBookIds, setInitialBookIds] = a.useState([]);
  const [bookFilter, setBookFilter] = a.useState("");

  const resetForm = () => {
    setNameAr("");
    setNameEn("");
    setSlug("");
    setPhotoUrl("");
    setBioAr("");
    setBioEn("");
    setDisplayOrder(0);
    setIsActive(!0);
    setSelectedBookIds([]);
    setInitialBookIds([]);
    setBookFilter("");
    setEditingAuthor(null);
    setModalOpen(!1);
  };

  const openCreate = () => {
    resetForm();
    setDisplayOrder(authors.length);
    setModalOpen(!0);
  };

  const openEdit = (author) => {
    setEditingAuthor(author);
    setNameAr(author.name_ar || "");
    setNameEn(author.name_en || "");
    setSlug(author.slug || "");
    setPhotoUrl(author.photo_url || "");
    setBioAr(author.bio_ar || "");
    setBioEn(author.bio_en || "");
    setDisplayOrder(author.display_order ?? 0);
    setIsActive(Boolean(author.is_active));

    // Find currently linked books in allBooks
    const linked = allBooks
      .filter(b => b.author_id === author.id || (b.author_ar === author.name_ar && !b.author_id))
      .map(b => b.id);
    setSelectedBookIds(linked);
    setInitialBookIds(linked);
    setBookFilter("");
    setModalOpen(!0);
  };

  const saveMutation = G({
    mutationFn: (payload) => postAuthor(payload),
    onSuccess: () => {
      v.success(isAr ? "تم حفظ بيانات المؤلف بنجاح" : "Author saved successfully");
      qc.invalidateQueries({ queryKey: ["admin", "authors"] });
      resetForm();
    },
    onError: (err) => {
      v.error(err?.message || (isAr ? "حدث خطأ أثناء الحفظ" : "Save failed"));
    }
  });

  const deleteMutation = G({
    mutationFn: (id) => delAuthor(id),
    onSuccess: () => {
      v.success(isAr ? "تم حذف المؤلف" : "Author deleted");
      qc.invalidateQueries({ queryKey: ["admin", "authors"] });
      setDeleteConfirmId(null);
    },
    onError: (err) => {
      v.error(err?.message || (isAr ? "تعذر الحذف" : "Delete failed"));
    }
  });

  const handleSave = () => {
    if (!nameAr.trim()) {
      v.error(isAr ? "يرجى كتابة اسم المؤلف بالعربية" : "Arabic name is required");
      return;
    }

    const unlinked = initialBookIds.filter(id => !selectedBookIds.includes(id));

    const payload = {
      ...(editingAuthor?.id ? { id: editingAuthor.id } : {}),
      name_ar: nameAr.trim(),
      name_en: nameEn.trim() || nameAr.trim(),
      slug: slug.trim(),
      photo_url: photoUrl.trim() || null,
      bio_ar: bioAr.trim() || null,
      bio_en: bioEn.trim() || null,
      display_order: Number(displayOrder) || 0,
      is_active: isActive,
      book_ids: selectedBookIds,
      unlinked_book_ids: unlinked
    };

    saveMutation.mutate(payload);
  };

  const toggleBook = (bookId) => {
    setSelectedBookIds(prev => 
      prev.includes(bookId) ? prev.filter(id => id !== bookId) : [...prev, bookId]
    );
  };

  // Filter authors list
  const filteredAuthors = a.useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return authors;
    return authors.filter(auth => 
      (auth.name_ar && auth.name_ar.toLowerCase().includes(q)) ||
      (auth.name_en && auth.name_en.toLowerCase().includes(q)) ||
      (auth.slug && auth.slug.toLowerCase().includes(q)) ||
      (auth.bio_ar && auth.bio_ar.toLowerCase().includes(q))
    );
  }, [authors, search]);

  // Filter books inside modal
  const modalBooks = a.useMemo(() => {
    const q = bookFilter.trim().toLowerCase();
    if (!q) return allBooks;
    return allBooks.filter(b => 
      (b.title_ar && b.title_ar.toLowerCase().includes(q)) ||
      (b.title_en && b.title_en.toLowerCase().includes(q))
    );
  }, [allBooks, bookFilter]);

  return e.jsxs("div", {
    className: "space-y-6 max-w-[1400px] mx-auto",
    children: [
      // Top header
      e.jsxs("div", {
        className: "flex flex-wrap items-center justify-between gap-4",
        children: [
          e.jsxs("div", {
            children: [
              e.jsxs("div", {
                className: "flex items-center gap-2",
                children: [
                  e.jsx(FeatherIcon, { className: "h-6 w-6 text-primary" }),
                  e.jsx("h1", {
                    className: "font-display font-black text-2xl md:text-3xl",
                    children: isAr ? "المؤلفون والكُتّاب" : "Authors & Writers"
                  })
                ]
              }),
              e.jsx("p", {
                className: "text-sm text-muted-foreground mt-1",
                children: query.isLoading
                  ? "..."
                  : isAr
                  ? `إجمالي ${authors.length} مؤلف مسجل في المتجر`
                  : `Total ${authors.length} authors registered`
              })
            ]
          }),
          e.jsxs("button", {
            onClick: openCreate,
            className: "h-10 px-4 rounded-xl bg-primary text-primary-foreground text-sm font-bold hover:bg-primary-hover flex items-center gap-2 shadow-sm transition-transform active:scale-95",
            children: [
              e.jsx(q, { className: "h-4 w-4" }),
              isAr ? "إضافة كاتب جديد" : "New Author"
            ]
          })
        ]
      }),

      // Search bar
      e.jsx("div", {
        className: "relative max-w-md",
        children: e.jsxs("div", {
          className: "relative",
          children: [
            e.jsx(SearchIcon, { className: "absolute top-1/2 -translate-y-1/2 start-3 h-4 w-4 text-muted-foreground" }),
            e.jsx("input", {
              type: "search",
              value: search,
              onChange: s => setSearch(s.target.value),
              placeholder: isAr ? "ابحث بالاسم، النبذة أو الرابط..." : "Search authors...",
              className: "w-full h-10 ps-9 pe-4 rounded-xl bg-card border border-border text-sm focus:outline-none focus:border-primary transition-colors"
            })
          ]
        })
      }),

      // Loading spinner
      query.isLoading && e.jsx("div", {
        className: "flex justify-center py-20",
        children: e.jsx(O, { className: "h-8 w-8 animate-spin text-primary" })
      }),

      // Empty state
      !query.isLoading && filteredAuthors.length === 0 && e.jsxs("div", {
        className: "flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed border-border text-center bg-card p-6",
        children: [
          e.jsx("div", { className: "text-5xl mb-4", children: "🪶" }),
          e.jsx("p", {
            className: "font-bold text-lg",
            children: search ? (isAr ? "لا يوجد مؤلف مطابق للبحث" : "No authors matched") : (isAr ? "لم تتم إضافة مؤلفين بعد" : "No authors yet")
          }),
          !search && e.jsx("button", {
            onClick: openCreate,
            className: "mt-4 h-10 px-5 rounded-xl bg-primary text-primary-foreground text-sm font-bold hover:bg-primary-hover shadow-sm",
            children: isAr ? "أضف أول كاتب" : "Add first author"
          })
        ]
      }),

      // Authors Grid
      !query.isLoading && filteredAuthors.length > 0 && e.jsx("div", {
        className: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5",
        children: filteredAuthors.map(author => {
          const initialLetter = (author.name_ar || author.name_en || "ك")[0];
          return e.jsxs("div", {
            className: "rounded-2xl border border-border bg-card shadow-card-soft hover:shadow-md transition-all p-5 flex flex-col justify-between space-y-4",
            children: [
              e.jsxs("div", {
                className: "flex items-start gap-3.5",
                children: [
                  // Photo or Monogram
                  e.jsx("div", {
                    className: "h-16 w-16 shrink-0 rounded-full border border-primary/20 bg-muted/40 overflow-hidden shadow-sm flex items-center justify-center",
                    children: author.photo_url
                      ? e.jsx("img", {
                          src: author.photo_url,
                          alt: author.name_ar,
                          className: "h-full w-full object-cover",
                          onError: t => { t.target.style.display = "none"; }
                        })
                      : e.jsx("span", {
                          className: "font-display font-black text-xl text-primary",
                          children: initialLetter
                        })
                  }),
                  // Name and info
                  e.jsxs("div", {
                    className: "flex-1 min-w-0",
                    children: [
                      e.jsxs("div", {
                        className: "flex items-center gap-2",
                        children: [
                          e.jsx("h3", {
                            className: "font-display font-bold text-lg text-foreground truncate",
                            children: isAr ? author.name_ar : (author.name_en || author.name_ar)
                          }),
                          !author.is_active && e.jsx("span", {
                            className: "px-2 py-0.5 rounded-full bg-muted text-muted-foreground text-[10px] font-semibold shrink-0",
                            children: isAr ? "مخفي" : "Hidden"
                          })
                        ]
                      }),
                      author.name_en && author.name_en !== author.name_ar && e.jsx("p", {
                        className: "text-xs text-muted-foreground truncate",
                        children: author.name_en
                      }),
                      e.jsxs("div", {
                        className: "flex items-center gap-2 text-xs text-muted-foreground mt-1",
                        children: [
                          e.jsxs("span", {
                            className: "inline-flex items-center gap-1 font-semibold text-primary",
                            children: [
                              e.jsx(BookIcon, { className: "h-3.5 w-3.5" }),
                              author.books_count ?? 0,
                              " ",
                              isAr ? "كتاب" : "books"
                            ]
                          }),
                          e.jsx("span", { children: "•" }),
                          e.jsxs("span", {
                            className: "truncate font-mono text-[11px]",
                            dir: "ltr",
                            children: ["/@", author.slug]
                          })
                        ]
                      })
                    ]
                  })
                ]
              }),

              // Bio snippet
              author.bio_ar && e.jsx("p", {
                className: "text-xs text-muted-foreground line-clamp-2 leading-relaxed bg-muted/20 p-2.5 rounded-xl border border-border/40",
                children: isAr ? author.bio_ar : (author.bio_en || author.bio_ar)
              }),

              // Actions Footer
              e.jsxs("div", {
                className: "pt-3 border-t border-border flex items-center justify-between gap-2",
                children: [
                  e.jsxs("a", {
                    href: `/author/${encodeURIComponent(author.slug || author.id)}`,
                    target: "_blank",
                    rel: "noopener noreferrer",
                    className: "inline-flex items-center gap-1.5 text-xs text-muted-foreground hover:text-primary transition-colors",
                    children: [
                      e.jsx(ExternalLinkIcon, { className: "h-3.5 w-3.5" }),
                      isAr ? "معاينة البروفايل" : "View profile"
                    ]
                  }),
                  e.jsxs("div", {
                    className: "flex items-center gap-1",
                    children: [
                      e.jsx("button", {
                        onClick: () => openEdit(author),
                        className: "grid h-8 w-8 place-items-center rounded-lg hover:bg-muted text-muted-foreground hover:text-foreground transition-colors",
                        title: isAr ? "تعديل" : "Edit",
                        children: e.jsx(R, { className: "h-4 w-4" })
                      }),
                      e.jsx("button", {
                        onClick: () => setDeleteConfirmId(author.id),
                        className: "grid h-8 w-8 place-items-center rounded-lg hover:bg-rose-50 text-muted-foreground hover:text-rose-600 dark:hover:bg-rose-950/30 transition-colors",
                        title: isAr ? "حذف" : "Delete",
                        children: e.jsx(V, { className: "h-4 w-4" })
                      })
                    ]
                  })
                ]
              })
            ]
          }, author.id);
        })
      }),

      // Add / Edit Modal
      modalOpen && e.jsxs("div", {
        className: "fixed inset-0 z-50 flex items-center justify-center p-4",
        children: [
          e.jsx("div", {
            className: "absolute inset-0 bg-foreground/50 backdrop-blur-sm",
            onClick: resetForm
          }),
          e.jsxs("div", {
            className: "relative bg-background rounded-3xl shadow-2xl w-full max-w-2xl p-6 space-y-5 max-h-[92vh] overflow-y-auto border border-border",
            children: [
              // Modal Title
              e.jsxs("div", {
                className: "flex items-center justify-between pb-3 border-b border-border",
                children: [
                  e.jsxs("div", {
                    className: "flex items-center gap-2",
                    children: [
                      e.jsx(FeatherIcon, { className: "h-5 w-5 text-primary" }),
                      e.jsx("h2", {
                        className: "font-display font-black text-xl",
                        children: editingAuthor
                          ? (isAr ? "تعديل بيانات الكاتب" : "Edit Author")
                          : (isAr ? "إضافة كاتب جديد" : "New Author")
                      })
                    ]
                  }),
                  e.jsx("button", {
                    onClick: resetForm,
                    className: "p-1.5 hover:bg-muted rounded-lg transition-colors",
                    children: e.jsx(ee, { className: "h-5 w-5" })
                  })
                ]
              }),

              // Photo Section
              e.jsxs("div", {
                className: "bg-card rounded-2xl p-4 border border-border space-y-3",
                children: [
                  e.jsx("label", {
                    className: "text-xs font-bold uppercase tracking-wider text-muted-foreground block",
                    children: isAr ? "صورة الكاتب الشخصية" : "Author Photo"
                  }),
                  e.jsx(ImageUpload, {
                    value: photoUrl,
                    onChange: setPhotoUrl,
                    folder: "authors",
                    size: 80
                  }),
                  e.jsx("input", {
                    type: "url",
                    value: photoUrl,
                    onChange: s => setPhotoUrl(s.target.value),
                    placeholder: isAr ? "أو الصق رابط صورة الكاتب مباشرة (https://...)" : "Or paste image URL...",
                    className: "w-full h-9 px-3 rounded-lg border border-input bg-background text-xs font-mono focus:outline-none focus:border-primary",
                    dir: "ltr"
                  })
                ]
              }),

              // Basic info: Names & Slug
              e.jsxs("div", {
                className: "grid grid-cols-1 sm:grid-cols-2 gap-4",
                children: [
                  e.jsxs("div", {
                    children: [
                      e.jsx("label", {
                        className: "text-sm font-semibold mb-1 block",
                        children: isAr ? "اسم الكاتب بالعربية *" : "Name (Arabic) *"
                      }),
                      e.jsx("input", {
                        value: nameAr,
                        onChange: s => {
                          setNameAr(s.target.value);
                          if (!editingAuthor && !slug) {
                            const gen = s.target.value.trim().toLowerCase().replace(/\s+/g, "-").replace(/[^\p{L}\p{N}-]/gu, "");
                            setSlug(gen);
                          }
                        },
                        placeholder: "مثال: د. أحمد خالد توفيق",
                        className: "w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:border-primary",
                        dir: "rtl"
                      })
                    ]
                  }),
                  e.jsxs("div", {
                    children: [
                      e.jsx("label", {
                        className: "text-sm font-semibold mb-1 block",
                        children: isAr ? "الاسم بالإنجليزية" : "Name (English)"
                      }),
                      e.jsx("input", {
                        value: nameEn,
                        onChange: s => setNameEn(s.target.value),
                        placeholder: "e.g. Ahmed Khaled Tawfik",
                        className: "w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:border-primary"
                      })
                    ]
                  })
                ]
              }),

              // Slug field
              e.jsxs("div", {
                children: [
                  e.jsxs("label", {
                    className: "text-sm font-semibold mb-1 block",
                    children: [
                      "الرابط التعريفي (Slug) ",
                      e.jsx("span", {
                        className: "text-xs text-muted-foreground font-normal",
                        children: isAr ? "(يظهر في رابط صفحة الكاتب: /author/slug)" : "(/author/slug)"
                      })
                    ]
                  }),
                  e.jsx("input", {
                    value: slug,
                    onChange: s => setSlug(s.target.value),
                    placeholder: "مثال: ahmed-khaled-tawfik",
                    className: "w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-mono focus:outline-none focus:border-primary",
                    dir: "ltr"
                  })
                ]
              }),

              // Bio Arabic
              e.jsxs("div", {
                children: [
                  e.jsx("label", {
                    className: "text-sm font-semibold mb-1 block",
                    children: isAr ? "نبذة وسيرة عن الكاتب (بالعربية)" : "Bio (Arabic)"
                  }),
                  e.jsx("textarea", {
                    value: bioAr,
                    onChange: s => setBioAr(s.target.value),
                    rows: 3,
                    placeholder: isAr ? "اكتب نبذة تعريفية عن الكاتب، نشأته، أسلوبه الأدبي وأهم إنجازاته..." : "About the author...",
                    className: "w-full px-3 py-2 rounded-xl border border-input bg-background text-sm focus:outline-none focus:border-primary leading-relaxed",
                    dir: "rtl"
                  })
                ]
              }),

              // Bio English (optional)
              e.jsxs("div", {
                children: [
                  e.jsx("label", {
                    className: "text-sm font-semibold mb-1 block",
                    children: isAr ? "النبذة بالإنجليزية (اختياري)" : "Bio (English - optional)"
                  }),
                  e.jsx("textarea", {
                    value: bioEn,
                    onChange: s => setBioEn(s.target.value),
                    rows: 2,
                    placeholder: "Author biography in English...",
                    className: "w-full px-3 py-2 rounded-xl border border-input bg-background text-sm focus:outline-none focus:border-primary leading-relaxed"
                  })
                ]
              }),

              // Multi-Book Linking Section
              e.jsxs("div", {
                className: "bg-muted/30 rounded-2xl p-4 border border-border space-y-3",
                children: [
                  e.jsxs("div", {
                    className: "flex items-center justify-between gap-2",
                    children: [
                      e.jsxs("div", {
                        children: [
                          e.jsx("label", {
                            className: "text-sm font-bold block",
                            children: isAr ? "ربط أعمال وكتب الكاتب في المتجر" : "Link Author Books"
                          }),
                          e.jsx("p", {
                            className: "text-xs text-muted-foreground",
                            children: isAr
                              ? "حدد الكتب والروايات التي تتبع هذا الكاتب لتظهر في بروفايله تلقائياً"
                              : "Select books that belong to this author"
                          })
                        ]
                      }),
                      e.jsxs("span", {
                        className: "px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold shrink-0",
                        children: [
                          selectedBookIds.length,
                          " ",
                          isAr ? "كتاب محدد" : "selected"
                        ]
                      })
                    ]
                  }),

                  // Quick filter for books
                  e.jsx("input", {
                    type: "search",
                    value: bookFilter,
                    onChange: s => setBookFilter(s.target.value),
                    placeholder: isAr ? "ابحث في كتب المتجر لتحديدها..." : "Filter store books...",
                    className: "w-full h-9 px-3 rounded-lg border border-input bg-background text-xs focus:outline-none focus:border-primary"
                  }),

                  // Books list
                  e.jsx("div", {
                    className: "max-h-52 overflow-y-auto space-y-1.5 pe-1 divide-y divide-border/40",
                    children: modalBooks.length === 0
                      ? e.jsx("p", {
                          className: "text-xs text-muted-foreground py-3 text-center",
                          children: isAr ? "لا توجد كتب مطابقة" : "No books found"
                        })
                      : modalBooks.map(b => {
                          const isSelected = selectedBookIds.includes(b.id);
                          return e.jsxs("label", {
                            className: `flex items-center gap-3 p-2 rounded-xl cursor-pointer transition-colors ${
                              isSelected ? "bg-primary/10 border border-primary/20" : "hover:bg-muted/50 border border-transparent"
                            }`,
                            children: [
                              e.jsx("input", {
                                type: "checkbox",
                                checked: isSelected,
                                onChange: () => toggleBook(b.id),
                                className: "h-4 w-4 rounded accent-primary shrink-0"
                              }),
                              b.cover_url && e.jsx("img", {
                                src: b.cover_url,
                                alt: "",
                                className: "h-8 w-6 object-cover rounded shadow-xs shrink-0"
                              }),
                              e.jsxs("div", {
                                className: "flex-1 min-w-0 text-xs",
                                children: [
                                  e.jsx("div", {
                                    className: "font-semibold text-foreground truncate",
                                    children: b.title_ar
                                  }),
                                  b.author_ar && b.author_ar !== "—" && e.jsxs("div", {
                                    className: "text-[11px] text-muted-foreground truncate",
                                    children: [isAr ? "المؤلف الحالي: " : "Current author: ", b.author_ar]
                                  })
                                ]
                              })
                            ]
                          }, b.id);
                        })
                  })
                ]
              }),

              // Controls: Display order & Active
              e.jsxs("div", {
                className: "grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1",
                children: [
                  e.jsxs("div", {
                    children: [
                      e.jsx("label", {
                        className: "text-sm font-semibold mb-1 block",
                        children: isAr ? "ترتيب الظهور" : "Display Order"
                      }),
                      e.jsx("input", {
                        type: "number",
                        value: displayOrder,
                        onChange: s => setDisplayOrder(s.target.value),
                        className: "w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:border-primary"
                      })
                    ]
                  }),
                  e.jsxs("div", {
                    className: "flex items-center justify-between sm:justify-end gap-3 sm:pt-6",
                    children: [
                      e.jsx("span", {
                        className: "text-sm font-semibold",
                        children: isAr ? "ظهور الملف الشخصي" : "Active / Visible"
                      }),
                      e.jsx("button", {
                        type: "button",
                        onClick: () => setIsActive(!isActive),
                        className: `relative h-6 w-11 rounded-full transition-colors ${
                          isActive ? "bg-primary" : "bg-muted"
                        }`,
                        children: e.jsx("span", {
                          className: `absolute top-0.5 ${isActive ? "end-0.5" : "start-0.5"} h-5 w-5 rounded-full bg-white shadow transition-all`
                        })
                      })
                    ]
                  })
                ]
              }),

              // Action Buttons
              e.jsxs("div", {
                className: "flex gap-3 pt-3 border-t border-border",
                children: [
                  e.jsx("button", {
                    type: "button",
                    onClick: resetForm,
                    className: "flex-1 h-11 rounded-xl border border-input text-sm font-bold hover:bg-muted transition-colors",
                    children: isAr ? "إلغاء" : "Cancel"
                  }),
                  e.jsxs("button", {
                    type: "button",
                    onClick: handleSave,
                    disabled: !nameAr.trim() || saveMutation.isPending,
                    className: "flex-1 h-11 rounded-xl bg-primary text-primary-foreground text-sm font-bold hover:bg-primary-hover disabled:opacity-50 flex items-center justify-center gap-2 shadow-sm transition-transform active:scale-98",
                    children: [
                      saveMutation.isPending
                        ? e.jsx(O, { className: "h-4 w-4 animate-spin" })
                        : e.jsx(se, { className: "h-4 w-4" }),
                      isAr ? "حفظ المؤلف" : "Save Author"
                    ]
                  })
                ]
              })
            ]
          })
        ]
      }),

      // Delete confirmation modal
      deleteConfirmId && e.jsxs("div", {
        className: "fixed inset-0 z-50 flex items-center justify-center p-4",
        children: [
          e.jsx("div", {
            className: "absolute inset-0 bg-foreground/50 backdrop-blur-sm",
            onClick: () => setDeleteConfirmId(null)
          }),
          e.jsxs("div", {
            className: "relative bg-background rounded-3xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4 border border-border",
            children: [
              e.jsx("div", { className: "text-4xl", children: "🗑️" }),
              e.jsx("h2", {
                className: "font-bold text-lg",
                children: isAr ? "حذف بيانات الكاتب؟" : "Delete author?"
              }),
              e.jsx("p", {
                className: "text-sm text-muted-foreground",
                children: isAr
                  ? "لن يتم حذف الكتب من المتجر، بل سيتم فصل ربطها بالمؤلف فقط."
                  : "Books will not be deleted, only unlinked."
              }),
              e.jsxs("div", {
                className: "flex gap-2 pt-2",
                children: [
                  e.jsx("button", {
                    onClick: () => setDeleteConfirmId(null),
                    className: "flex-1 h-10 rounded-xl border border-input text-sm font-semibold hover:bg-muted",
                    children: isAr ? "إلغاء" : "Cancel"
                  }),
                  e.jsxs("button", {
                    onClick: () => deleteMutation.mutate(deleteConfirmId),
                    disabled: deleteMutation.isPending,
                    className: "flex-1 h-10 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 flex items-center justify-center gap-2 disabled:opacity-60",
                    children: [
                      deleteMutation.isPending && e.jsx(O, { className: "h-4 w-4 animate-spin" }),
                      isAr ? "تأكيد الحذف" : "Delete"
                    ]
                  })
                ]
              })
            ]
          })
        ]
      })
    ]
  });
}

export { AdminAuthors as component };
