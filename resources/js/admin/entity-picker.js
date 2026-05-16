/**
 * AJAX entity picker: hidden ID + search input + dropdown results.
 * Markup: .entity-picker with data-search-url, data-hidden-name, optional data-initial-*.
 */
(function () {
    function debounce(fn, ms) {
        var t;
        return function () {
            var args = arguments;
            var ctx = this;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, ms);
        };
    }

    function closeAllDropdowns(except) {
        document.querySelectorAll('.entity-picker-results.show').forEach(function (el) {
            if (el !== except) {
                el.classList.remove('show');
            }
        });
    }

    function formatProductLabel(p, stockLabel) {
        return p.name + ' (' + p.sku + ') — ' + stockLabel + ' ' + p.stock_quantity;
    }

    function initPicker(root) {
        var searchUrl = root.getAttribute('data-search-url');
        var hiddenName = root.getAttribute('data-hidden-name');
        var pickerType = root.getAttribute('data-type') || 'customer';
        var stockLabel = root.getAttribute('data-stock-label') || 'stock';
        var minLen = parseInt(root.getAttribute('data-min-length') || '1', 10);
        var initialId = root.getAttribute('data-initial-id') || '';
        var initialLabel = root.getAttribute('data-initial-label') || '';

        var hidden = root.querySelector('input[type="hidden"]');
        var input = root.querySelector('.entity-picker-input');
        var results = root.querySelector('.entity-picker-results');
        var hint = root.querySelector('.entity-picker-hint');

        if (!hidden || !input || !results || !searchUrl) {
            return;
        }

        if (hiddenName) {
            hidden.setAttribute('name', hiddenName);
        }

        if (initialId) {
            hidden.value = initialId;
            input.value = initialLabel;
        }

        function selectItem(id, label, extra) {
            hidden.value = id;
            input.value = label;
            results.classList.remove('show');
            results.innerHTML = '';
            if (hint && extra && extra.stock !== undefined) {
                hint.textContent = stockLabel + ' ' + extra.stock;
                hint.classList.remove('d-none');
            } else if (hint) {
                hint.classList.add('d-none');
            }
            hidden.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function renderItems(items) {
            results.innerHTML = '';
            if (!items.length) {
                results.innerHTML = '<div class="list-group-item disabled text-muted small">' +
                    (root.getAttribute('data-empty-label') || 'No results') + '</div>';
                results.classList.add('show');
                return;
            }
            items.forEach(function (item) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                if (pickerType === 'product') {
                    btn.textContent = formatProductLabel(item, stockLabel);
                    btn.addEventListener('click', function () {
                        selectItem(String(item.id), formatProductLabel(item, stockLabel), { stock: item.stock_quantity });
                    });
                } else {
                    var label = item.name + (item.phone ? ' (' + item.phone + ')' : '');
                    btn.textContent = label;
                    btn.addEventListener('click', function () {
                        selectItem(String(item.id), label, null);
                    });
                }
                results.appendChild(btn);
            });
            results.classList.add('show');
        }

        var doSearch = debounce(function () {
            var q = input.value.trim();
            if (q.length < minLen) {
                results.classList.remove('show');
                results.innerHTML = '';
                return;
            }
            fetch(searchUrl + (searchUrl.indexOf('?') >= 0 ? '&' : '?') + 'q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) { return r.json(); })
                .then(renderItems)
                .catch(function () {
                    results.innerHTML = '';
                    results.classList.remove('show');
                });
        }, 250);

        input.addEventListener('input', function () {
            if (!input.value.trim()) {
                hidden.value = '';
                if (hint) {
                    hint.classList.add('d-none');
                }
            }
            doSearch();
        });

        input.addEventListener('focus', function () {
            closeAllDropdowns(results);
            if (input.value.trim().length >= minLen) {
                doSearch();
            }
        });

        root.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    function initAll() {
        document.querySelectorAll('.entity-picker').forEach(initPicker);
        document.addEventListener('click', function () {
            closeAllDropdowns(null);
        });
    }

    window.initEntityPickers = initAll;
    window.initEntityPicker = function (root) {
        if (root && root.classList && root.classList.contains('entity-picker')) {
            initPicker(root);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
