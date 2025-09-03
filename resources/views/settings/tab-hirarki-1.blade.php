<div class="row">
    <div class="col-lg-12">
        <!-- UI awal: Struktur Organisasi Perusahaan (hanya tampilan, tanpa flow sistem) -->
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="m-0">Struktur Organisasi</h5>
                    <small class="text-muted">Tampilan awal untuk mengatur hirarki karyawan</small>
                </div>
                <div class="hirarki-toolbar d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="min-width: 260px;">
                        <span class="input-group-text">Cari</span>
                        <input id="hirarki-search" type="text" class="form-control"
                            placeholder="Cari posisi/karyawan..." autocomplete="off" />
                        <button class="btn btn-outline-secondary" type="button" id="hirarki-clear">×</button>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btn-expand-all" type="button" class="btn btn-outline-primary">Perluas</button>
                        <button id="btn-collapse-all" type="button" class="btn btn-outline-secondary">Ciutkan</button>
                    </div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Zoom">
                        <button id="btn-zoom-out" type="button" class="btn btn-outline-secondary"
                            title="Zoom Out">−</button>
                        <button id="btn-zoom-reset" type="button" class="btn btn-outline-secondary"
                            title="Reset Zoom">100%</button>
                        <button id="btn-zoom-in" type="button" class="btn btn-outline-secondary"
                            title="Zoom In">+</button>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-success" disabled
                            title="Belum diimplementasikan">+ Tambah Posisi</button>
                        <button type="button" class="btn btn-outline-warning" disabled
                            title="Belum diimplementasikan">Ubah</button>
                        <button type="button" class="btn btn-outline-danger" disabled
                            title="Belum diimplementasikan">Hapus</button>
                    </div>
                </div>
            </div>
            <div id="hirarki-container" class="card-body">
                <div class="hirarki-layout">
                    <div class="hirarki-tree card-like">
                        <div class="hirarki-tree-scroll">
                            <!-- Org Chart: contoh statis untuk tampilan awal -->
                            <ul class="hirarki-org">
                                <li>
                                    <div class="node" data-id="1" data-level="Direktur Utama">
                                        <span class="toggle" title="Expand/Collapse"></span>
                                        <div class="node-box">
                                            <div class="node-title">Direktur Utama</div>
                                            <div class="node-sub">CEO</div>
                                        </div>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="node" data-id="2" data-level="Divisi">
                                                <span class="toggle" title="Expand/Collapse"></span>
                                                <div class="node-box">
                                                    <div class="node-title">HR & GA</div>
                                                    <div class="node-sub">Divisi</div>
                                                </div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="node" data-id="3" data-level="Manager">
                                                        <span class="toggle" title="Expand/Collapse"></span>
                                                        <div class="node-box">
                                                            <div class="node-title">HR Manager</div>
                                                            <div class="node-sub">Manager</div>
                                                        </div>
                                                    </div>
                                                    <ul>
                                                        <li>
                                                            <div class="node" data-id="4" data-level="Staff">
                                                                <div class="node-box">
                                                                    <div class="node-title">Recruiter</div>
                                                                    <div class="node-sub">Staff</div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="node" data-id="5" data-level="Staff">
                                                                <div class="node-box">
                                                                    <div class="node-title">Payroll</div>
                                                                    <div class="node-sub">Staff</div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div class="node" data-id="6" data-level="Divisi">
                                                <span class="toggle" title="Expand/Collapse"></span>
                                                <div class="node-box">
                                                    <div class="node-title">Engineering</div>
                                                    <div class="node-sub">Divisi</div>
                                                </div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="node" data-id="7" data-level="Manager">
                                                        <span class="toggle" title="Expand/Collapse"></span>
                                                        <div class="node-box">
                                                            <div class="node-title">Engineering Manager</div>
                                                            <div class="node-sub">Manager</div>
                                                        </div>
                                                    </div>
                                                    <ul>
                                                        <li>
                                                            <div class="node" data-id="8" data-level="Lead">
                                                                <span class="toggle" title="Expand/Collapse"></span>
                                                                <div class="node-box">
                                                                    <div class="node-title">Backend Lead</div>
                                                                    <div class="node-sub">Lead</div>
                                                                </div>
                                                            </div>
                                                            <ul>
                                                                <li>
                                                                    <div class="node" data-id="9"
                                                                        data-level="Engineer">
                                                                        <div class="node-box">
                                                                            <div class="node-title">Backend Engineer
                                                                            </div>
                                                                            <div class="node-sub">Engineer</div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                        <li>
                                                            <div class="node" data-id="10" data-level="Lead">
                                                                <span class="toggle" title="Expand/Collapse"></span>
                                                                <div class="node-box">
                                                                    <div class="node-title">Frontend Lead</div>
                                                                    <div class="node-sub">Lead</div>
                                                                </div>
                                                            </div>
                                                            <ul>
                                                                <li>
                                                                    <div class="node" data-id="11"
                                                                        data-level="Engineer">
                                                                        <div class="node-box">
                                                                            <div class="node-title">Frontend Engineer
                                                                            </div>
                                                                            <div class="node-sub">Engineer</div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div class="node" data-id="12" data-level="Divisi">
                                                <span class="toggle" title="Expand/Collapse"></span>
                                                <div class="node-box">
                                                    <div class="node-title">Finance</div>
                                                    <div class="node-sub">Divisi</div>
                                                </div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="node" data-id="13" data-level="Manager">
                                                        <span class="toggle" title="Expand/Collapse"></span>
                                                        <div class="node-box">
                                                            <div class="node-title">Finance Manager</div>
                                                            <div class="node-sub">Manager</div>
                                                        </div>
                                                    </div>
                                                    <ul>
                                                        <li>
                                                            <div class="node" data-id="14" data-level="Staff">
                                                                <div class="node-box">
                                                                    <div class="node-title">Accountant</div>
                                                                    <div class="node-sub">Staff</div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="node" data-id="15" data-level="Divisi">
                                        <span class="toggle" title="Expand/Collapse"></span>
                                        <div class="node-box">
                                            <div class="node-title">Operasional</div>
                                            <div class="node-sub">Divisi</div>
                                        </div>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="node" data-id="16" data-level="Manager">
                                                <span class="toggle" title="Expand/Collapse"></span>
                                                <div class="node-box">
                                                    <div class="node-title">Operasional Manager</div>
                                                    <div class="node-sub">Manager</div>
                                                </div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="node" data-id="17" data-level="Staff">
                                                        <div class="node-box">
                                                            <div class="node-title">Supervisor Operasional</div>
                                                            <div class="node-sub">Staff</div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="node" data-id="18" data-level="Staff">
                                                        <div class="node-box">
                                                            <div class="node-title">Operator</div>
                                                            <div class="node-sub">Staff</div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="node" data-id="19" data-level="Divisi">
                                        <span class="toggle" title="Expand/Collapse"></span>
                                        <div class="node-box">
                                            <div class="node-title">Sales &amp; Marketing</div>
                                            <div class="node-sub">Divisi</div>
                                        </div>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="node" data-id="20" data-level="Manager">
                                                <span class="toggle" title="Expand/Collapse"></span>
                                                <div class="node-box">
                                                    <div class="node-title">Sales Manager</div>
                                                    <div class="node-sub">Manager</div>
                                                </div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="node" data-id="21" data-level="Staff">
                                                        <div class="node-box">
                                                            <div class="node-title">Sales Executive</div>
                                                            <div class="node-sub">Staff</div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div class="node" data-id="22" data-level="Manager">
                                                <span class="toggle" title="Expand/Collapse"></span>
                                                <div class="node-box">
                                                    <div class="node-title">Marketing Manager</div>
                                                    <div class="node-sub">Manager</div>
                                                </div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <div class="node" data-id="23" data-level="Staff">
                                                        <div class="node-box">
                                                            <div class="node-title">Marketing Specialist</div>
                                                            <div class="node-sub">Staff</div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="hirarki-side card-like">
                        <div class="side-header">Detail Posisi</div>
                        <div class="side-body">
                            <div class="placeholder">Pilih sebuah node pada struktur untuk melihat detail.</div>
                            <dl class="row small m-0" id="detail-list" hidden>
                                <dt class="col-5">Nama</dt>
                                <dd class="col-7" data-field="nama">-</dd>
                                <dt class="col-5">Level</dt>
                                <dd class="col-7" data-field="level">-</dd>
                                <dt class="col-5">Jalur</dt>
                                <dd class="col-7" data-field="jalur">-</dd>
                                <dt class="col-5">Jumlah Bawahan</dt>
                                <dd class="col-7" data-field="bawahan">-</dd>
                            </dl>
                            <hr />
                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-outline-success" disabled
                                    title="Belum diimplementasikan">+
                                    Tambah Bawahan</button>
                                <button class="btn btn-sm btn-outline-warning" disabled
                                    title="Belum diimplementasikan">Ubah Posisi</button>
                                <button class="btn btn-sm btn-outline-danger" disabled
                                    title="Belum diimplementasikan">Hapus Posisi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Scoped styles for hierarchy UI */
            /* unify vertical spacing across levels */
            #hirarki-container {
                --level-gap: 28px;
                --zoom: 1;
            }

            #hirarki-container .card-like {
                background: var(--bs-body-bg, #fff);
                border: 1px solid var(--bs-border-color, #dee2e6);
                border-radius: var(--bs-border-radius, 0.375rem);
            }

            #hirarki-container .hirarki-layout {
                display: grid;
                grid-template-columns: 1fr minmax(260px, 320px);
                gap: 16px;
            }

            #hirarki-container .hirarki-tree {
                min-height: 420px;
            }

            #hirarki-container .hirarki-tree-scroll {
                overflow: auto;
                padding: 16px;
            }

            /* scale the whole org for zoom */
            .hirarki-tree-scroll .hirarki-org {
                transform: scale(var(--zoom));
                transform-origin: 50% 0%;
                display: inline-block;
                /* ensure transform box wraps content */
            }

            #hirarki-container .hirarki-side .side-header {
                padding: 12px 16px;
                border-bottom: 1px solid var(--bs-border-color, #dee2e6);
                font-weight: 600;
            }

            #hirarki-container .hirarki-side .side-body {
                padding: 12px 16px;
            }

            #hirarki-container .placeholder {
                color: var(--bs-secondary-color, #6c757d);
                font-style: italic;
            }

            /* Org chart (UL-based) */
            .hirarki-org,
            .hirarki-org ul {
                padding-top: var(--level-gap);
                position: relative;
                padding-left: 0;
            }

            /* keep top-level divisions on a single line to avoid dropping below */
            .hirarki-org>li>ul {
                white-space: nowrap;
            }

            .hirarki-org>li>ul ul {
                white-space: normal;
            }

            .hirarki-org li {
                display: inline-block;
                vertical-align: top;
                /* ensure siblings align to top (same depth) */
                text-align: center;
                list-style-type: none;
                position: relative;
                padding: var(--level-gap) 8px 0 8px;
            }

            /* connectors */
            .hirarki-org li::before,
            .hirarki-org li::after {
                content: '';
                position: absolute;
                top: -1px;
                /* overlap to avoid visible gap with vertical connector */
                right: 50%;
                border-top: 1px solid var(--bs-border-color, #dee2e6);
                width: 50%;
                height: var(--level-gap);
            }

            .hirarki-org li::after {
                right: auto;
                left: 50%;
                border-left: 1px solid var(--bs-border-color, #dee2e6);
            }

            .hirarki-org li:only-child::after,
            .hirarki-org li:only-child::before {
                display: none;
            }

            .hirarki-org li:only-child {
                padding-top: 0;
            }

            .hirarki-org li:first-child::before,
            .hirarki-org li:last-child::after {
                border: 0 none;
            }

            .hirarki-org li:last-child::before {
                border-right: 1px solid var(--bs-border-color, #dee2e6);
                border-radius: 0 5px 0 0;
            }

            .hirarki-org li:first-child::after {
                border-radius: 5px 0 0 0;
            }

            /* vertical lines */
            .hirarki-org ul::before {
                content: '';
                position: absolute;
                top: 0;
                left: 50%;
                border-left: 1px solid var(--bs-border-color, #dee2e6);
                width: 0;
                height: calc(var(--level-gap) + 1px);
                /* slight overlap with horizontal connectors */
            }

            /* node */
            .node {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .node .toggle {
                width: 14px;
                height: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--bs-border-color, #dee2e6);
                border-radius: 3px;
                cursor: pointer;
                user-select: none;
                background: var(--bs-tertiary-bg, #f8f9fa);
            }

            .node .toggle::after {
                content: '\2212';
                font-size: 10px;
                line-height: 1;
                color: var(--bs-secondary-color, #6c757d);
            }

            .node.collapsed>.toggle::after {
                content: '\002B';
            }

            .node-box {
                display: inline-block;
                padding: 8px 10px;
                border: 1px solid var(--bs-border-color, #dee2e6);
                border-radius: var(--bs-border-radius, 0.375rem);
                background: var(--bs-body-bg, #ffffff);
                min-width: 160px;
                box-shadow: var(--bs-box-shadow-sm, 0 .125rem .25rem rgba(0, 0, 0, 0.075));
                text-align: left;
            }

            .node-box .node-title {
                font-weight: 600;
                font-size: 0.95rem;
                color: var(--bs-body-color, #212529);
            }

            .node-box .node-sub {
                font-size: 0.75rem;
                color: var(--bs-secondary-color, #6c757d);
            }

            .node.selected .node-box {
                border-color: var(--bs-primary, #0d6efd);
                box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb, 13, 110, 253), .15);
            }

            /* collapsed branch */
            li.collapsed>ul {
                display: none;
            }

            li.collapsed>.node>.toggle::after {
                content: '\002B';
            }

            /* search highlight */
            .node-box.mark {
                background: var(--bs-warning-bg-subtle, #fff3cd);
                border-color: var(--bs-warning, #ffc107);
            }

            /* use Bootstrap utilities for gap and grid; no overrides here */

            /* responsive */
            @media (max-width: 992px) {
                #hirarki-container .hirarki-layout {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <script>
            (function() {
                const root = document.getElementById('hirarki-container');
                if (!root) return;

                const tree = root.querySelector('.hirarki-org');
                const searchInput = root.querySelector('#hirarki-search');
                const clearBtn = root.querySelector('#hirarki-clear');
                const expandAllBtn = root.querySelector('#btn-expand-all');
                const collapseAllBtn = root.querySelector('#btn-collapse-all');
                const zoomOutBtn = root.querySelector('#btn-zoom-out');
                const zoomInBtn = root.querySelector('#btn-zoom-in');
                const zoomResetBtn = root.querySelector('#btn-zoom-reset');
                const detail = root.querySelector('#detail-list');
                const placeholder = root.querySelector('.placeholder');

                function liFromNode(nodeEl) {
                    return nodeEl ? nodeEl.closest('li') : null;
                }

                function isLeaf(li) {
                    return !li || !li.querySelector(':scope > ul');
                }

                function toggleBranch(li, collapse) {
                    if (!li) return;
                    if (collapse === undefined) {
                        li.classList.toggle('collapsed');
                        li.querySelector(':scope > .node')?.classList.toggle('collapsed');
                    } else {
                        li.classList.toggle('collapsed', !!collapse);
                        li.querySelector(':scope > .node')?.classList.toggle('collapsed', !!collapse);
                    }
                }

                function setAll(collapse) {
                    tree.querySelectorAll('li').forEach(li => toggleBranch(li, collapse));
                }

                function pathTo(li) {
                    const names = [];
                    let cur = li;
                    while (cur) {
                        const name = cur.querySelector(':scope > .node .node-title')?.textContent?.trim();
                        if (name) names.unshift(name);
                        cur = cur.parentElement?.closest('li') || null;
                    }
                    return names.join(' > ');
                }

                function countChildren(li) {
                    return li ? li.querySelectorAll(':scope > ul > li').length : 0;
                }

                function showDetail(li) {
                    const node = li?.querySelector(':scope > .node');
                    if (!node) return;
                    const nama = node.querySelector('.node-title')?.textContent?.trim() || '-';
                    const level = node.getAttribute('data-level') || '-';
                    const jalur = pathTo(li);
                    const bawahan = countChildren(li);

                    detail.querySelector('[data-field="nama"]').textContent = nama;
                    detail.querySelector('[data-field="level"]').textContent = level;
                    detail.querySelector('[data-field="jalur"]').textContent = jalur;
                    detail.querySelector('[data-field="bawahan"]').textContent = bawahan;
                    placeholder.hidden = true;
                    detail.hidden = false;
                }

                // Node selection and toggle
                root.addEventListener('click', (e) => {
                    const toggleEl = e.target.closest('.toggle');
                    if (toggleEl) {
                        const li = liFromNode(toggleEl);
                        if (li && !isLeaf(li)) {
                            toggleBranch(li);
                        }
                        e.preventDefault();
                        return;
                    }

                    const nodeEl = e.target.closest('.node');
                    if (nodeEl && root.contains(nodeEl)) {
                        root.querySelectorAll('.node.selected').forEach(n => n.classList.remove('selected'));
                        nodeEl.classList.add('selected');
                        showDetail(liFromNode(nodeEl));
                    }
                });

                // Search/filter
                function normalize(s) {
                    return (s || '').toLowerCase();
                }

                function clearSearchMarks() {
                    tree.querySelectorAll('.node-box.mark').forEach(n => n.classList.remove('mark'));
                }

                function applySearch(q) {
                    const term = normalize(q);
                    clearSearchMarks();
                    if (!term) {
                        // restore (do not auto-expand when cleared)
                        tree.querySelectorAll('li').forEach(li => {
                            li.style.display = '';
                        });
                        return;
                    }
                    // show matches and their ancestors, hide others
                    tree.querySelectorAll('li').forEach(li => li.style.display = 'none');
                    const nodes = Array.from(tree.querySelectorAll('.node'));
                    nodes.forEach(node => {
                        const title = normalize(node.querySelector('.node-title')?.textContent);
                        const sub = normalize(node.querySelector('.node-sub')?.textContent);
                        if (title.includes(term) || sub.includes(term)) {
                            node.querySelector('.node-box')?.classList.add('mark');
                            let li = liFromNode(node);
                            while (li) {
                                li.style.display = '';
                                const parent = li.parentElement?.closest('li');
                                if (parent) {
                                    toggleBranch(parent, false);
                                }
                                li = parent;
                            }
                        }
                    });
                }

                searchInput?.addEventListener('input', (e) => applySearch(e.target.value));
                clearBtn?.addEventListener('click', () => {
                    searchInput.value = '';
                    applySearch('');
                    searchInput.focus();
                });

                // Expand/Collapse all
                expandAllBtn?.addEventListener('click', () => setAll(false));
                collapseAllBtn?.addEventListener('click', () => setAll(true));

                // Zoom controls
                let zoom = 1.0;
                const ZOOM_MIN = 0.5;
                const ZOOM_MAX = 2.0;
                const ZOOM_STEP = 0.1;

                function applyZoom() {
                    root.style.setProperty('--zoom', String(zoom));
                    if (zoomResetBtn) zoomResetBtn.textContent = Math.round(zoom * 100) + '%';
                }

                function clamp(v, min, max) {
                    return Math.min(max, Math.max(min, v));
                }
                zoomOutBtn?.addEventListener('click', () => {
                    zoom = clamp(parseFloat((zoom - ZOOM_STEP).toFixed(2)), ZOOM_MIN, ZOOM_MAX);
                    applyZoom();
                });
                zoomInBtn?.addEventListener('click', () => {
                    zoom = clamp(parseFloat((zoom + ZOOM_STEP).toFixed(2)), ZOOM_MIN, ZOOM_MAX);
                    applyZoom();
                });
                zoomResetBtn?.addEventListener('click', () => {
                    zoom = 1.0;
                    applyZoom();
                });

                // Initialize: collapse all except root level
                (function init() {
                    const top = tree.querySelector(':scope > li');
                    tree.querySelectorAll('li').forEach(li => {
                        if (li !== top) toggleBranch(li, true);
                    });
                    // open the first children level
                    top?.querySelectorAll(':scope > ul > li').forEach(li => toggleBranch(li, false));
                    applyZoom();
                })();
            })();
        </script>
    </div>
</div>
