(function () {
  var root = document.querySelector(".lm-nm");
  if (!root) return;

  var featured = root.querySelector(".lm-nm__featured");
  var head = root.querySelector(".lm-nm__head");
  var grid = root.querySelector(".lm-nm__grid");
  var more = root.querySelector(".lm-nm__more");
  var moreBtn = more.querySelector("button");
  var search = root.querySelector(".lm-nm__search");
  var pills = root.querySelectorAll(".lm-pill");
  var viewAll = root.querySelector(".lm-viewall");

  var state = { cat: root.getAttribute("data-cat") || "all", s: "", page: 1 };
  var reqId = 0,
    timer;

  /* Scroll-in reveal with stagger */
  var io =
    "IntersectionObserver" in window
      ? new IntersectionObserver(
          function (entries) {
            entries.forEach(function (e) {
              if (e.isIntersecting) {
                e.target.classList.add("is-in");
                io.unobserve(e.target);
              }
            });
          },
          { threshold: 0.08, rootMargin: "0px 0px -40px 0px" },
        )
      : null;

  function reveal(scope) {
    var els = scope.querySelectorAll(".lm-reveal:not(.is-in)");
    Array.prototype.forEach.call(els, function (el, i) {
      el.style.setProperty("--i", i % 5);
      if (io) io.observe(el);
      else el.classList.add("is-in");
    });
  }

  function load(append) {
    var id = ++reqId;
    root.classList.add("is-loading");
    moreBtn.disabled = true;

    var body = new URLSearchParams({
      action: "lm_news_filter",
      cat: state.cat,
      s: state.s,
      page: state.page,
    });

    fetch(lmNews.ajax, { method: "POST", body: body })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        if (id !== reqId || !res.success) return;
        var d = res.data;
        if (append) {
          grid.insertAdjacentHTML("beforeend", d.cards);
        } else {
          featured.innerHTML = d.featured;
          grid.innerHTML = d.cards;
          head.style.display = d.total > 1 ? "" : "none";
        }
        more.hidden = !d.has_more;
        reveal(root);
      })
      .catch(function () {})
      .then(function () {
        if (id === reqId) {
          root.classList.remove("is-loading");
          moreBtn.disabled = false;
        }
      });
  }

  function setUrl() {
    var url = new URL(window.location.href);
    if (state.cat === "all") url.searchParams.delete("cat");
    else url.searchParams.set("cat", state.cat);
    history.replaceState(null, "", url);
  }

  function setActive(cat) {
    pills.forEach(function (x) {
      x.classList.toggle("is-active", x.getAttribute("data-cat") === cat);
    });
  }

  pills.forEach(function (p) {
    p.addEventListener("click", function () {
      if (p.classList.contains("is-active") && !state.s) return;
      state.cat = p.getAttribute("data-cat");
      state.page = 1;
      setActive(state.cat);
      setUrl();
      load(false);
    });
  });

  search.addEventListener("input", function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      state.s = search.value.trim();
      state.page = 1;
      load(false);
    }, 350);
  });

  moreBtn.addEventListener("click", function () {
    state.page++;
    load(true);
  });

  /* "View All News & Media": plain link if all_url was set, otherwise reset filters */
  if (viewAll && !viewAll.hasAttribute("data-link")) {
    viewAll.addEventListener("click", function (e) {
      e.preventDefault();
      state.cat = "all";
      state.s = "";
      state.page = 1;
      search.value = "";
      setActive("all");
      setUrl();
      load(false);
    });
  }

  reveal(root); // animate the first render
})();
