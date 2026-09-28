import { c as React, l as e, o as useStore, p as cn } from "./index-Dmn91ErK.js";

const { useState, useEffect, useCallback } = React;

function FileIcon({ className }) {
  return e.jsx("svg", {
    className,
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    children: [
      e.jsx("path", { d: "M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" }),
      e.jsx("path", { d: "M14 2v4a2 2 0 0 0 2 2h4" }),
      e.jsx("path", { d: "M10 9H8" }),
      e.jsx("path", { d: "M16 13H8" }),
      e.jsx("path", { d: "M16 17H8" })
    ]
  });
}

function DownloadIcon({ className }) {
  return e.jsx("svg", {
    className,
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    children: [
      e.jsx("path", { d: "M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" }),
      e.jsx("polyline", { points: "7 10 12 15 17 10" }),
      e.jsx("line", { x1: "12", x2: "12", y1: "15", y2: "3" })
    ]
  });
}

function WhatsAppIcon({ className }) {
  return e.jsx("svg", {
    className,
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    children: [
      e.jsx("path", { d: "M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" })
    ]
  });
}

function TrashIcon({ className }) {
  return e.jsx("svg", {
    className,
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    children: [
      e.jsx("path", { d: "M3 6h18" }),
      e.jsx("path", { d: "M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" }),
      e.jsx("path", { d: "M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" })
    ]
  });
}

function EyeIcon({ className }) {
  return e.jsx("svg", {
    className,
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    children: [
      e.jsx("path", { d: "M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" }),
      e.jsx("circle", { cx: "12", cy: "12", r: "3" })
    ]
  });
}

function CloseIcon({ className }) {
  return e.jsx("svg", {
    className,
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    children: [
      e.jsx("path", { d: "M18 6 6 18" }),
      e.jsx("path", { d: "m6 6 12 12" })
    ]
  });
}

const STATUS_CONFIG = {
  pending: { label_ar: "جديد / معلق", label_en: "Pending", bg: "bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20" },
  reviewing: { label_ar: "قيد المراجعة", label_en: "Reviewing", bg: "bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20" },
  contacted: { label_ar: "تم التواصل", label_en: "Contacted", bg: "bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20" },
  accepted: { label_ar: "مقبول للنشر", label_en: "Accepted", bg: "bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20" },
  rejected: { label_ar: "معتذر عنه", label_en: "Rejected", bg: "bg-red-500/10 text-red-700 dark:text-red-400 border-red-500/20" }
};

function AdminManuscriptsPage() {
  const isAr = useStore((s) => s.locale) === "ar";
  const [manuscripts, setManuscripts] = useState([]);
  const [counts, setCounts] = useState({ total: 0, pending_count: 0, reviewing_count: 0, contacted_count: 0, accepted_count: 0, rejected_count: 0 });
  const [loading, setLoading] = useState(true);
  const [filterStatus, setFilterStatus] = useState("all");
  const [search, setSearch] = useState("");
  const [selectedItem, setSelectedItem] = useState(null);
  const [modalNotes, setModalNotes] = useState("");
  const [savingNotes, setSavingNotes] = useState(false);

  const fetchManuscripts = useCallback(async () => {
    setLoading(true);
    try {
      const q = new URLSearchParams();
      if (filterStatus !== "all") q.set("status", filterStatus);
      if (search.trim()) q.set("search", search.trim());
      
      const res = await fetch("/api/admin/manuscripts.php?" + q.toString());
      const data = await res.json();
      if (data && data.ok) {
        setManuscripts(data.data?.manuscripts || []);
        if (data.data?.counts) setCounts(data.data.counts);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  }, [filterStatus, search]);

  useEffect(() => {
    fetchManuscripts();
  }, [fetchManuscripts]);

  const handleStatusChange = async (id, newStatus) => {
    try {
      const res = await fetch("/api/admin/manuscripts.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, status: newStatus })
      });
      if (res.ok) {
        setManuscripts((prev) =>
          prev.map((item) => (item.id === id ? { ...item, status: newStatus } : item))
        );
        if (selectedItem && selectedItem.id === id) {
          setSelectedItem((prev) => ({ ...prev, status: newStatus }));
        }
      }
    } catch (e) {
      console.error(e);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm(isAr ? "هل أنت متأكد من حذف هذا الطلب؟" : "Are you sure you want to delete this submission?")) {
      return;
    }
    try {
      const res = await fetch("/api/admin/manuscripts.php?id=" + id, { method: "DELETE" });
      if (res.ok) {
        setManuscripts((prev) => prev.filter((item) => item.id !== id));
        if (selectedItem && selectedItem.id === id) setSelectedItem(null);
      }
    } catch (e) {
      console.error(e);
    }
  };

  const openDetails = (item) => {
    setSelectedItem(item);
    setModalNotes(item.notes || "");
  };

  const handleSaveNotes = async () => {
    if (!selectedItem) return;
    setSavingNotes(true);
    try {
      const res = await fetch("/api/admin/manuscripts.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: selectedItem.id, notes: modalNotes })
      });
      if (res.ok) {
        setManuscripts((prev) =>
          prev.map((item) => (item.id === selectedItem.id ? { ...item, notes: modalNotes } : item))
        );
        setSelectedItem((prev) => ({ ...prev, notes: modalNotes }));
      }
    } catch (e) {
      console.error(e);
    } finally {
      setSavingNotes(false);
    }
  };

  return e.jsxs("div", {
    className: "p-6 md:p-8 space-y-8 max-w-7xl mx-auto",
    children: [
      // Header
      e.jsxs("div", {
        className: "flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/60 pb-6",
        children: [
          e.jsxs("div", {
            children: [
              e.jsx("h1", {
                className: "font-display font-black text-2xl md:text-3xl text-foreground mb-1",
                children: isAr ? "طلبات النشر والمخطوطات" : "Manuscript Submissions"
              }),
              e.jsx("p", {
                className: "text-sm text-muted-foreground",
                children: isAr
                  ? "إدارة ومتابعة طلبات المؤلفين الراغبين في النشر مع دار مدينة الأدباء، وتحميل المخطوطات والتواصل المباشر."
                  : "Manage author manuscript submissions, download files, and track evaluation progress."
              })
            ]
          }),
          e.jsx("button", {
            onClick: fetchManuscripts,
            className: "self-start md:self-auto px-4 py-2 rounded-xl border border-input bg-card hover:bg-muted text-sm font-semibold transition-colors flex items-center gap-2",
            children: [
              e.jsx("span", { children: "🔄" }),
              e.jsx("span", { children: isAr ? "تحديث القائمة" : "Refresh" })
            ]
          })
        ]
      }),

      // Stats Counters
      e.jsxs("div", {
        className: "grid grid-cols-2 md:grid-cols-5 gap-4",
        children: [
          e.jsxs("div", {
            className: "bg-card border border-border rounded-2xl p-4 text-center",
            children: [
              e.jsx("p", { className: "text-xs text-muted-foreground mb-1", children: isAr ? "إجمالي الطلبات" : "Total" }),
              e.jsx("p", { className: "font-display font-black text-2xl text-foreground", children: counts.total || 0 })
            ]
          }),
          e.jsxs("div", {
            className: "bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 text-center",
            children: [
              e.jsx("p", { className: "text-xs text-amber-700 dark:text-amber-400 font-semibold mb-1", children: isAr ? "جديد / معلق" : "Pending" }),
              e.jsx("p", { className: "font-display font-black text-2xl text-amber-700 dark:text-amber-400", children: counts.pending_count || 0 })
            ]
          }),
          e.jsxs("div", {
            className: "bg-blue-500/10 border border-blue-500/20 rounded-2xl p-4 text-center",
            children: [
              e.jsx("p", { className: "text-xs text-blue-700 dark:text-blue-400 font-semibold mb-1", children: isAr ? "قيد المراجعة" : "Reviewing" }),
              e.jsx("p", { className: "font-display font-black text-2xl text-blue-700 dark:text-blue-400", children: counts.reviewing_count || 0 })
            ]
          }),
          e.jsxs("div", {
            className: "bg-purple-500/10 border border-purple-500/20 rounded-2xl p-4 text-center",
            children: [
              e.jsx("p", { className: "text-xs text-purple-700 dark:text-purple-400 font-semibold mb-1", children: isAr ? "تم التواصل" : "Contacted" }),
              e.jsx("p", { className: "font-display font-black text-2xl text-purple-700 dark:text-purple-400", children: counts.contacted_count || 0 })
            ]
          }),
          e.jsxs("div", {
            className: "bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 text-center",
            children: [
              e.jsx("p", { className: "text-xs text-emerald-700 dark:text-emerald-400 font-semibold mb-1", children: isAr ? "مقبول للنشر" : "Accepted" }),
              e.jsx("p", { className: "font-display font-black text-2xl text-emerald-700 dark:text-emerald-400", children: counts.accepted_count || 0 })
            ]
          })
        ]
      }),

      // Filters and Search
      e.jsxs("div", {
        className: "flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-card border border-border p-4 rounded-2xl",
        children: [
          // Filter Tabs
          e.jsxs("div", {
            className: "flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none",
            children: [
              { key: "all", label_ar: "الكل", label_en: "All" },
              { key: "pending", label_ar: "جديد", label_en: "Pending" },
              { key: "reviewing", label_ar: "قيد المراجعة", label_en: "Reviewing" },
              { key: "contacted", label_ar: "تم التواصل", label_en: "Contacted" },
              { key: "accepted", label_ar: "مقبول", label_en: "Accepted" },
              { key: "rejected", label_ar: "معتذر عنه", label_en: "Rejected" }
            ].map((tab) => e.jsx("button", {
              key: tab.key,
              onClick: () => setFilterStatus(tab.key),
              className: cn(
                "px-3.5 py-1.5 rounded-xl text-xs md:text-sm font-bold whitespace-nowrap transition-colors",
                filterStatus === tab.key
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground"
              ),
              children: isAr ? tab.label_ar : tab.label_en
            }))
          }),

          // Search Box
          e.jsx("div", {
            className: "relative min-w-[240px]",
            children: e.jsx("input", {
              type: "text",
              value: search,
              onChange: (e) => setSearch(e.target.value),
              placeholder: isAr ? "بحث بالاسم، الهاتف، الإيميل..." : "Search name, phone, email...",
              className: "w-full h-10 px-4 rounded-xl border border-input bg-background text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
            })
          })
        ]
      }),

      // Table of Submissions
      e.jsx("div", {
        className: "bg-card border border-border rounded-2xl overflow-hidden shadow-sm",
        children: loading ? e.jsx("div", {
          className: "py-16 text-center text-muted-foreground",
          children: isAr ? "جاري تحميل الطلبات..." : "Loading submissions..."
        }) : manuscripts.length === 0 ? e.jsx("div", {
          className: "py-16 text-center text-muted-foreground space-y-2",
          children: [
            e.jsx("p", { className: "text-3xl", children: "📭" }),
            e.jsx("p", { className: "font-semibold", children: isAr ? "لا توجد طلبات نشر حالياً." : "No submissions found." })
          ]
        }) : e.jsx("div", {
          className: "overflow-x-auto",
          children: e.jsxs("table", {
            className: "w-full text-sm text-start border-collapse",
            children: [
              e.jsx("thead", {
                className: "bg-muted/50 border-b border-border text-xs text-muted-foreground uppercase font-semibold",
                children: e.jsxs("tr", {
                  children: [
                    e.jsx("th", { className: "px-5 py-3.5 text-start", children: isAr ? "المؤلف والطلب" : "Author & Details" }),
                    e.jsx("th", { className: "px-5 py-3.5 text-start", children: isAr ? "التواصل المباشر" : "Contact" }),
                    e.jsx("th", { className: "px-5 py-3.5 text-start", children: isAr ? "المخطوط المرفق" : "Manuscript File" }),
                    e.jsx("th", { className: "px-5 py-3.5 text-start", children: isAr ? "الحالة" : "Status" }),
                    e.jsx("th", { className: "px-5 py-3.5 text-center", children: isAr ? "إجراءات" : "Actions" })
                  ]
                })
              }),
              e.jsx("tbody", {
                className: "divide-y divide-border/60",
                children: manuscripts.map((item) => {
                  const statusCfg = STATUS_CONFIG[item.status] || STATUS_CONFIG.pending;
                  const dateStr = item.created_at ? new Date(item.created_at).toLocaleDateString(isAr ? "ar-EG" : "en-US", { year: "numeric", month: "short", day: "numeric", hour: "2-digit", minute: "2-digit" }) : "";
                  const cleanPhone = (item.phone || "").replace(/[^0-9+]/g, "");
                  const whatsappUrl = cleanPhone ? "https://wa.me/" + (cleanPhone.startsWith("+") ? cleanPhone.slice(1) : (cleanPhone.startsWith("0") ? "2" + cleanPhone : cleanPhone)) : null;

                  return e.jsxs("tr", {
                    key: item.id,
                    className: "hover:bg-muted/30 transition-colors",
                    children: [
                      // Author & Date
                      e.jsxs("td", {
                        className: "px-5 py-4",
                        children: [
                          e.jsx("div", {
                            className: "font-bold text-foreground text-base mb-0.5",
                            children: item.name
                          }),
                          e.jsx("div", {
                            className: "text-xs text-muted-foreground font-mono",
                            children: dateStr
                          })
                        ]
                      }),

                      // Contact info with quick WhatsApp
                      e.jsxs("td", {
                        className: "px-5 py-4",
                        children: [
                          e.jsxs("div", {
                            className: "flex items-center gap-2 mb-1",
                            children: [
                              e.jsx("span", { className: "font-mono text-sm", dir: "ltr", children: item.phone }),
                              whatsappUrl ? e.jsx("a", {
                                href: whatsappUrl,
                                target: "_blank",
                                rel: "noopener noreferrer",
                                title: isAr ? "محادثة واتساب مباشرة" : "Chat on WhatsApp",
                                className: "inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm",
                                children: e.jsx(WhatsAppIcon, { className: "w-4 h-4" })
                              }) : null
                            ]
                          }),
                          e.jsx("a", {
                            href: "mailto:" + item.email,
                            className: "text-xs text-muted-foreground hover:text-primary transition-colors block truncate max-w-[200px]",
                            children: item.email
                          })
                        ]
                      }),

                      // Attached manuscript download
                      e.jsx("td", {
                        className: "px-5 py-4",
                        children: item.file_path ? e.jsxs("a", {
                          href: "/" + item.file_path,
                          target: "_blank",
                          download: item.file_name || true,
                          className: "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-primary/20 bg-primary/5 hover:bg-primary/10 text-primary text-xs font-bold transition-all shadow-sm group",
                          children: [
                            e.jsx(DownloadIcon, { className: "w-4 h-4 group-hover:scale-110 transition-transform" }),
                            e.jsxs("span", {
                              className: "truncate max-w-[150px]",
                              children: [item.file_name || (isAr ? "تحميل المخطوط" : "Download File")]
                            }),
                            item.file_size ? e.jsxs("span", {
                              className: "text-[10px] text-muted-foreground",
                              children: ["(", (item.file_size / (1024 * 1024)).toFixed(1), " MB)"]
                            }) : null
                          ]
                        }) : e.jsx("span", {
                          className: "text-xs text-muted-foreground italic",
                          children: isAr ? "بدون ملف مرفق" : "No file attached"
                        })
                      }),

                      // Status Dropdown
                      e.jsx("td", {
                        className: "px-5 py-4",
                        children: e.jsxs("select", {
                          value: item.status || "pending",
                          onChange: (e) => handleStatusChange(item.id, e.target.value),
                          className: cn(
                            "px-3 py-1 rounded-xl text-xs font-bold border outline-none cursor-pointer transition-all",
                            statusCfg.bg
                          ),
                          children: [
                            e.jsx("option", { value: "pending", children: isAr ? "جديد / معلق" : "Pending" }),
                            e.jsx("option", { value: "reviewing", children: isAr ? "قيد المراجعة" : "Reviewing" }),
                            e.jsx("option", { value: "contacted", children: isAr ? "تم التواصل" : "Contacted" }),
                            e.jsx("option", { value: "accepted", children: isAr ? "مقبول للنشر" : "Accepted" }),
                            e.jsx("option", { value: "rejected", children: isAr ? "معتذر عنه" : "Rejected" })
                          ]
                        })
                      }),

                      // Actions
                      e.jsx("td", {
                        className: "px-5 py-4 text-center",
                        children: e.jsxs("div", {
                          className: "flex items-center justify-center gap-1.5",
                          children: [
                            // View details button
                            e.jsx("button", {
                              onClick: () => openDetails(item),
                              className: "p-2 rounded-xl text-muted-foreground hover:text-primary hover:bg-muted transition-colors",
                              title: isAr ? "عرض التفاصيل والرسالة" : "View details",
                              children: e.jsx(EyeIcon, { className: "w-4 h-4" })
                            }),
                            // Delete button
                            e.jsx("button", {
                              onClick: () => handleDelete(item.id),
                              className: "p-2 rounded-xl text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors",
                              title: isAr ? "حذف الطلب" : "Delete submission",
                              children: e.jsx(TrashIcon, { className: "w-4 h-4" })
                            })
                          ]
                        })
                      })
                    ]
                  });
                })
              })
            ]
          })
        })
      }),

      // Details Modal
      selectedItem ? e.jsx("div", {
        className: "fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200",
        children: e.jsxs("div", {
          className: "bg-card border border-border rounded-3xl shadow-2xl max-w-2xl w-full p-6 md:p-8 max-h-[90vh] overflow-y-auto space-y-6 relative",
          children: [
            // Close Button
            e.jsx("button", {
              onClick: () => setSelectedItem(null),
              className: "absolute top-5 end-5 p-2 rounded-xl text-muted-foreground hover:text-foreground hover:bg-muted transition-colors",
              children: e.jsx(CloseIcon, { className: "w-5 h-5" })
            }),

            // Title
            e.jsxs("div", {
              children: [
                e.jsx("h2", {
                  className: "font-display font-black text-2xl text-foreground mb-1",
                  children: selectedItem.name
                }),
                e.jsxs("p", {
                  className: "text-xs text-muted-foreground",
                  children: [isAr ? "رقم الطلب: #" : "Submission ID: #", selectedItem.id, " • ", new Date(selectedItem.created_at).toLocaleString(isAr ? "ar-EG" : "en-US")]
                })
              ]
            }),

            // Author Info Grid
            e.jsxs("div", {
              className: "grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-muted/40 border border-border/60 text-sm",
              children: [
                e.jsxs("div", {
                  children: [
                    e.jsx("span", { className: "text-xs text-muted-foreground block mb-0.5", children: isAr ? "الهاتف / الواتساب:" : "Phone / WhatsApp:" }),
                    e.jsx("span", { className: "font-bold text-foreground font-mono", dir: "ltr", children: selectedItem.phone })
                  ]
                }),
                e.jsxs("div", {
                  children: [
                    e.jsx("span", { className: "text-xs text-muted-foreground block mb-0.5", children: isAr ? "البريد الإلكتروني:" : "Email:" }),
                    e.jsx("a", { href: "mailto:" + selectedItem.email, className: "font-semibold text-primary hover:underline", children: selectedItem.email })
                  ]
                }),
                selectedItem.file_path ? e.jsxs("div", {
                  className: "md:col-span-2 pt-2 border-t border-border/40",
                  children: [
                    e.jsx("span", { className: "text-xs text-muted-foreground block mb-1.5", children: isAr ? "الملف المرفق:" : "Attached Manuscript:" }),
                    e.jsxs("a", {
                      href: "/" + selectedItem.file_path,
                      target: "_blank",
                      download: selectedItem.file_name || true,
                      className: "inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-primary-foreground text-xs font-bold shadow hover:bg-primary-hover transition-all",
                      children: [
                        e.jsx(DownloadIcon, { className: "w-4 h-4" }),
                        e.jsx("span", { children: isAr ? "تحميل المخطوط (" + (selectedItem.file_name || "الملف") + ")" : "Download Manuscript (" + (selectedItem.file_name || "file") + ")" })
                      ]
                    })
                  ]
                }) : null
              ]
            }),

            // Message / Synopsis
            e.jsxs("div", {
              children: [
                e.jsx("h3", {
                  className: "font-display font-bold text-base text-foreground mb-2",
                  children: isAr ? "ملخص العمل ورسالة الكاتب:" : "Manuscript Synopsis & Message:"
                }),
                e.jsx("div", {
                  className: "p-4 rounded-2xl bg-background border border-border/80 text-sm leading-relaxed whitespace-pre-wrap text-foreground/90 max-h-60 overflow-y-auto",
                  children: selectedItem.message || (isAr ? "لا توجد رسالة مرفقة." : "No message provided.")
                })
              ]
            }),

            // Internal Editorial Notes
            e.jsxs("div", {
              children: [
                e.jsx("h3", {
                  className: "font-display font-bold text-base text-foreground mb-2",
                  children: isAr ? "ملاحظات فريق القراءة والتحرير (خاصة بالدار):" : "Internal Editorial Notes:"
                }),
                e.jsx("textarea", {
                  rows: 3,
                  value: modalNotes,
                  onChange: (e) => setModalNotes(e.target.value),
                  placeholder: isAr ? "أضف ملاحظات داخلية حول تقييم العمل وقرار لجنة النشر..." : "Add internal evaluation notes...",
                  className: "w-full p-3.5 rounded-2xl border border-input bg-background text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all resize-y"
                }),
                e.jsx("div", {
                  className: "pt-2 flex justify-end",
                  children: e.jsx("button", {
                    onClick: handleSaveNotes,
                    disabled: savingNotes,
                    className: "px-4 py-2 rounded-xl bg-primary text-primary-foreground text-xs font-bold hover:bg-primary-hover transition-colors",
                    children: savingNotes ? (isAr ? "جاري الحفظ..." : "Saving...") : (isAr ? "حفظ الملاحظات" : "Save Notes")
                  })
                })
              ]
            })
          ]
        })
      }) : null
    ]
  });
}

export { AdminManuscriptsPage as component };
