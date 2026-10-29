@props(['title' => config('app.name', 'StudySpace')])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title }} · {{ config('app.name', 'StudySpace') }}</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #172033;
            --muted: #697386;
            --line: #e6eaf0;
            --paper: #ffffff;
            --canvas: #f5f7fb;
            --brand: #5145cd;
            --brand-dark: #4136b5;
            --brand-soft: #efedff;
            --green: #18845b;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body { min-width: 320px; min-height: 100vh; margin: 0; color: var(--ink); background: var(--canvas); }
        a { color: var(--brand); text-decoration: none; }
        a:hover { color: var(--brand-dark); }
        button, input, select, textarea { font: inherit; }
        button, [type="submit"] {
            min-height: 40px;
            padding: 9px 15px;
            border: 1px solid var(--brand);
            border-radius: 10px;
            color: #fff;
            background: var(--brand);
            font-weight: 650;
            cursor: pointer;
            transition: background .16s ease, border-color .16s ease, transform .16s ease;
        }
        button:hover, [type="submit"]:hover { border-color: var(--brand-dark); background: var(--brand-dark); }
        button:active, [type="submit"]:active { transform: translateY(1px); }
        :focus-visible { outline: 3px solid #aaa4ff; outline-offset: 3px; }

        .skip-link {
            position: fixed;
            z-index: 10;
            top: 12px;
            left: 12px;
            padding: 10px 14px;
            transform: translateY(-160%);
            border-radius: 9px;
            color: #fff;
            background: var(--ink);
        }
        .skip-link:focus { transform: translateY(0); }
        .topbar {
            position: sticky;
            z-index: 5;
            top: 0;
            border-bottom: 1px solid rgba(230, 234, 240, .9);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(16px);
        }
        .topbar-inner {
            display: flex;
            width: min(1160px, calc(100% - 48px));
            min-height: 74px;
            margin: 0 auto;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .brand {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 11px;
            color: var(--ink);
            font-size: 17px;
            font-weight: 760;
            letter-spacing: -.035em;
        }
        .brand-mark {
            display: grid;
            width: 37px;
            height: 37px;
            place-items: center;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(145deg, #7569f2, #5145cd);
            box-shadow: 0 5px 12px rgba(81, 69, 205, .22);
        }
        .brand-mark svg { width: 20px; height: 20px; }
        .nav-area, .nav-actions { display: flex; align-items: center; }
        .nav-area { gap: 12px; }
        .nav-actions { gap: 10px; }
        .nav-actions form { margin: 0; }
        .account-menu { position: relative; }
        .account-menu summary { list-style: none; cursor: pointer; }
        .account-menu summary::-webkit-details-marker { display: none; }
        .account-trigger { display: inline-flex; min-height: 44px; align-items: center; gap: 10px; padding: 5px 12px 5px 6px; border: 1px solid var(--line); border-radius: 999px; color: #354056; background: #fff; font-size: 12px; font-weight: 680; transition: border-color .16s ease, box-shadow .16s ease, background .16s ease; }
        .account-trigger:hover, .account-menu[open] .account-trigger, .account-menu:focus-within .account-trigger { border-color: #c9c4ff; background: #faf9ff; box-shadow: 0 4px 12px rgba(81, 69, 205, .08); }
        .account-avatar { display: grid; width: 31px; height: 31px; place-items: center; border-radius: 50%; color: #fff; background: linear-gradient(145deg, #7569f2, #5145cd); font-size: 12px; font-weight: 750; }
        .account-trigger-copy { display: grid; gap: 2px; text-align: left; }
        .account-trigger-copy small { color: #8992a3; font-size: 9px; font-weight: 600; }
        .account-chevron { width: 7px; height: 7px; margin-left: 4px; transform: rotate(45deg) translateY(-2px); border-right: 1.5px solid #8992a3; border-bottom: 1.5px solid #8992a3; transition: transform .16s ease; }
        .account-menu[open] .account-chevron { transform: rotate(225deg) translate(-1px, -1px); }
        .account-dropdown { position: absolute; z-index: 8; top: calc(100% + 8px); right: 0; display: none; min-width: 210px; gap: 5px; padding: 8px; border: 1px solid var(--line); border-radius: 14px; background: #fff; box-shadow: 0 16px 38px rgba(22, 32, 51, .15); }
        .account-menu:hover .account-dropdown, .account-menu:focus-within .account-dropdown, .account-menu[open] .account-dropdown { display: grid; }
        .account-dropdown-header { padding: 9px 10px 11px; border-bottom: 1px solid #edf0f5; }
        .account-dropdown-header strong, .account-dropdown-header small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .account-dropdown-header strong { color: #252d40; font-size: 12px; }
        .account-dropdown-header small { margin-top: 4px; color: #8992a3; font-size: 10px; }
        .account-dropdown a, .account-dropdown button { display: flex; width: 100%; min-height: 39px; align-items: center; gap: 9px; padding: 0 10px; border: 0; border-radius: 9px; color: #485267; background: transparent; font-size: 12px; font-weight: 620; text-align: left; }
        .account-dropdown a:hover { color: var(--brand); background: #f7f6ff; }
        .account-dropdown button { cursor: pointer; }
        .account-dropdown button:hover { color: #ad3131; background: #fff4f4; }
        .account-dropdown form { margin: 0; }
        .notification-link { position: relative; display: grid; width: 42px; height: 42px; place-items: center; border: 1px solid var(--line); border-radius: 12px; color: #596276; background: #fff; transition: border-color .16s ease, color .16s ease, background .16s ease; }
        .notification-link:hover { border-color: #c9c4ff; color: var(--brand); background: #faf9ff; }
        .notification-link svg { width: 19px; height: 19px; }
        .notification-badge { position: absolute; top: -5px; right: -5px; display: grid; min-width: 18px; height: 18px; padding: 0 4px; place-items: center; border: 2px solid #fff; border-radius: 999px; color: #fff; background: #d84352; font-size: 9px; font-weight: 800; line-height: 1; }
        .nav-button {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 9px;
            color: #485267;
            background: #fff;
            font-size: 12px;
            font-weight: 650;
            white-space: nowrap;
            cursor: pointer;
        }
        .nav-button:hover { border-color: #c9c4ff; color: var(--brand); background: #faf9ff; }
        .session-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: #596276;
            background: #fff;
            font-size: 11px;
            font-weight: 650;
            white-space: nowrap;
        }
        .session-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--green); }

        .site-main { width: min(1080px, calc(100% - 48px)); min-height: calc(100vh - 150px); margin: 0 auto; padding: 44px 0 64px; }
        .workspace-shell { display: grid; grid-template-columns: 224px minmax(0, 1fr); align-items: start; gap: 28px; }
        .workspace-content { min-width: 0; }
        .workspace-content > :first-child { margin-top: 0; }
        .workspace-sidebar {
            position: sticky;
            top: 98px;
            display: flex;
            min-height: min(680px, calc(100vh - 122px));
            overflow: hidden;
            flex-direction: column;
            border: 1px solid #e6eaf0;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(22, 32, 51, .035);
        }
        .sidebar-heading { display: flex; align-items: center; gap: 11px; padding: 19px 16px; border-bottom: 1px solid #edf0f5; }
        .sidebar-heading-icon { display: grid; width: 37px; height: 37px; flex: 0 0 auto; place-items: center; border-radius: 11px; color: #fff; background: linear-gradient(145deg,#7569f2,#5145cd); }
        .sidebar-heading-icon svg { width: 20px; height: 20px; }
        .sidebar-heading-copy, .sidebar-account-copy { display: grid; min-width: 0; gap: 3px; }
        .sidebar-heading-copy strong { color: #252d40; font-size: 12px; }
        .sidebar-heading-copy small { color: #8a92a2; font-size: 10px; }
        .sidebar-nav { display: grid; align-content: start; gap: 5px; padding: 17px 11px; }
        .sidebar-label { margin: 0 8px 7px !important; color: #9aa1af !important; font-size: 9px; font-weight: 750; letter-spacing: .14em; }
        .sidebar-link { display: flex; min-height: 42px; align-items: center; gap: 10px; padding: 0 10px; border-radius: 9px; color: #697386; font-size: 11px; font-weight: 620; transition: color .15s ease, background .15s ease; }
        .sidebar-link:hover { color: #5145cd; background: #f7f6ff; }
        .sidebar-link.is-active { color: #5145cd; background: #efedff; }
        .sidebar-link-icon { display: grid; width: 20px; height: 20px; flex: 0 0 auto; place-items: center; }
        .sidebar-link-icon svg { width: 18px; height: 18px; }
        .sidebar-active-dot { width: 6px; height: 6px; margin-left: auto; border-radius: 50%; background: #5145cd; }
        .sidebar-bottom { margin-top: auto; padding: 12px; border-top: 1px solid #edf0f5; }
        .sidebar-account { display: flex; align-items: center; gap: 10px; padding: 5px; }
        .sidebar-avatar { display: grid; width: 33px; height: 33px; flex: 0 0 auto; place-items: center; border: 1px solid #e5e2ff; border-radius: 50%; color: #5145cd; background: #f1efff; font-size: 12px; font-weight: 750; }
        .sidebar-account-copy strong { overflow: hidden; color: #333c50; font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
        .sidebar-account-copy small { color: #8a92a2; font-size: 9px; }
        .site-main h1 { margin: 0 0 12px; font-size: clamp(28px, 4vw, 40px); line-height: 1.12; letter-spacing: -.045em; }
        .site-main h2 { color: var(--ink); letter-spacing: -.025em; }
        .site-main p { color: var(--muted); line-height: 1.65; }
        .site-main section:not([class]), .site-main article:not([class]), .panel, .subject-card {
            margin: 22px 0;
            padding: clamp(20px, 4vw, 30px);
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--paper);
            box-shadow: 0 8px 24px rgba(22, 32, 51, .035);
        }
        .panel { margin-top: 0; }
        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }
        .section-header.compact { margin-bottom: 16px; }
        .eyebrow {
            margin: 0 0 8px;
            color: #766beb;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .section-header h1, .section-header h2 {
            margin: 0;
        }
        .button-link, .inline-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 9px;
            color: #485267;
            background: #fff;
            font-size: 12px;
            font-weight: 650;
        }
        .button-link:hover, .inline-link:hover { border-color: #c9c4ff; color: var(--brand); background: #faf9ff; }
        .button-link-primary { border-color: var(--brand); color: #fff; background: var(--brand); }
        .button-link-primary:hover { border-color: var(--brand-dark); color: #fff; background: var(--brand-dark); }
        .muted-pill {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border: 1px solid #edf0f5;
            border-radius: 999px;
            color: #60708b;
            background: #f7f9fc;
            font-size: 11px;
            font-weight: 700;
        }
        .status-message, .error-box {
            margin: 0 0 18px;
            padding: 13px 16px;
            border-radius: 12px;
        }
        .status-message {
            border: 1px solid #bce8d3;
            color: #176344;
            background: #effaf4;
        }
        .error-box {
            border: 1px solid #ffd0d0;
            color: #a33131;
            background: #fff5f5;
        }
        .error-box ul { margin: 0; padding-left: 18px; }
        .stack-form { margin: 0; }
        .input-row {
            display: grid;
            grid-template-columns: minmax(200px, 1fr) minmax(220px, 1.5fr);
            gap: 16px;
        }
        .subject-table-wrap { overflow-x: auto; }
        .subject-table { width: 100%; border-collapse: collapse; text-align: left; }
        .subject-table th, .subject-table td { padding: 14px 16px; border-bottom: 1px solid #edf0f5; vertical-align: top; }
        .subject-table thead th { color: #697386; background: #f8f9fc; font-size: 11px; font-weight: 750; letter-spacing: .04em; text-transform: uppercase; }
        .subject-table tbody th { color: var(--ink); font-weight: 700; }
        .subject-table tbody td { color: #697386; font-size: 13px; }
        .subject-table .subject-empty { padding: 28px 16px; text-align: center; }
        .note-preview { min-width: 180px; }
        .attachment-link { display: inline-block; margin: 2px 0; overflow-wrap: anywhere; }
        .note-actions { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
        .note-actions form { margin: 0; }
        .note-actions button { margin: 0 !important; }
        .button-danger { border-color: #c43f48; color: #fff; background: #c43f48; }
        .button-danger:hover { border-color: #a8323b; color: #fff; background: #a8323b; }
        .form-help { margin: 4px 0 12px !important; font-size: 12px; }
        .attachment-list { margin: 12px 0; padding: 14px 16px; border: 1px solid var(--line); border-radius: 10px; }
        .attachment-list legend { padding: 0 6px; color: #4c566b; font-size: 13px; font-weight: 650; }
        .attachment-list ul { display: grid; gap: 8px; margin: 0; padding-left: 20px; }
        .attachment-list li { color: #697386; font-size: 13px; }
        .dashboard-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 22px; }
        .dashboard-header h1 { margin-bottom: 8px; }
        .dashboard-header > div:first-child > p:last-child { margin: 0; }
        .dashboard-quick-actions { display: flex; flex-wrap: wrap; gap: 9px; }
        .dashboard-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 22px; }
        .dashboard-stats.admin-stats { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        .dashboard-stat-card { display: grid; gap: 8px; padding: 19px; border: 1px solid var(--line); border-radius: 14px; background: #fff; box-shadow: 0 8px 24px rgba(22, 32, 51, .035); }
        .dashboard-stat-label, .dashboard-card-kicker { color: #8992a3; font-size: 10px; font-weight: 750; letter-spacing: .1em; text-transform: uppercase; }
        .dashboard-stat-card strong { color: #252d40; font-size: 28px; line-height: 1; }
        .dashboard-stat-card strong.dashboard-stat-account { font-size: 20px; }
        .dashboard-stat-card a { width: fit-content; font-size: 11px; font-weight: 650; }
        .dashboard-section { margin-top: 0; }
        .dashboard-section h2 { margin: 0; font-size: 21px; }
        .dashboard-card-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px; }
        .dashboard-subject-card { min-width: 0; padding: 17px; border: 1px solid #edf0f5; border-radius: 12px; background: #fbfcff; }
        .dashboard-subject-card h3, .dashboard-note h3 { margin: 7px 0 5px; color: #252d40; font-size: 15px; overflow-wrap: anywhere; }
        .dashboard-subject-card p { margin: 0 0 12px; font-size: 12px; overflow-wrap: anywhere; }
        .dashboard-note-list { display: grid; }
        .dashboard-note { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 17px 0; border-bottom: 1px solid #edf0f5; }
        .dashboard-note:first-child { padding-top: 0; }
        .dashboard-note:last-child { padding-bottom: 0; border-bottom: 0; }
        .dashboard-note-copy { min-width: 0; }
        .dashboard-note h3 { margin-top: 0; }
        .dashboard-note-copy > p { margin: 7px 0 0; font-size: 12px; overflow-wrap: anywhere; }
        .dashboard-note-copy .dashboard-note-meta { display: flex; flex-wrap: wrap; gap: 7px; margin: 0; color: #8992a3; font-size: 11px; }
        .dashboard-attachments { display: flex; flex-wrap: wrap; gap: 5px 12px; margin-top: 8px; }
        .dashboard-attachments a { font-size: 11px; }
        .dashboard-profile-email { margin: -7px 0 14px; font-size: 12px; }
        .dashboard-section .empty-state .button-link { margin-top: 10px; }
        .notification-summary { margin: 7px 0 0; font-size: 13px; }
        .notification-card { display: flex; align-items: flex-start; gap: 14px; padding: 18px 4px; border-bottom: 1px solid #edf0f5; }
        .notification-card:last-of-type { border-bottom: 0; }
        .notification-status-dot { width: 9px; height: 9px; flex: 0 0 auto; margin-top: 7px; border-radius: 50%; background: transparent; }
        .notification-card.is-unread .notification-status-dot { background: #5145cd; box-shadow: 0 0 0 4px #efedff; }
        .notification-copy { flex: 1; min-width: 0; }
        .notification-copy h2 { margin: 0 0 5px; font-size: 15px; }
        .notification-copy p { margin: 0 0 7px; font-size: 13px; overflow-wrap: anywhere; }
        .notification-copy time { color: #8992a3; font-size: 11px; }
        .notification-card.is-read .notification-copy h2 { color: #606b80; }
        .notification-card form { margin: 0; }
        .notification-open-button { cursor: pointer; }
        .dashboard-list-footer { margin: 16px 0 0; text-align: right; font-size: 12px; font-weight: 650; }
        .dashboard-stat-note { color: #8992a3; font-size: 11px; }
        .admin-page-intro { margin: 7px 0 0; font-size: 13px; }
        .pagination { margin-top: 18px; }
        .pagination nav { display: flex; justify-content: center; }
        .user-details-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin: 0; }
        .user-details-grid > div { min-width: 0; padding: 14px; border: 1px solid #edf0f5; border-radius: 11px; background: #fbfcff; }
        .user-details-grid dt { margin-bottom: 6px; color: #8992a3; font-size: 10px; font-weight: 750; letter-spacing: .07em; text-transform: uppercase; }
        .user-details-grid dd { margin: 0; color: #252d40; font-size: 13px; font-weight: 650; overflow-wrap: anywhere; }
        .admin-record-card { margin-top: 16px; padding: 20px; border: 1px solid #edf0f5; border-radius: 13px; background: #fbfcff; }
        .admin-record-card h3 { margin: 0 0 6px; color: #252d40; font-size: 16px; }
        .admin-record-card .section-header { align-items: flex-start; margin-bottom: 12px; }
        .admin-record-card .section-header p { margin: 4px 0 10px; font-size: 12px; }
        .admin-record-card > form:last-child { margin-bottom: 0; }
        .admin-note-body { white-space: pre-wrap; overflow-wrap: anywhere; }
        .admin-attachment-list { display: grid; gap: 6px; margin: 10px 0 16px; padding-left: 20px; font-size: 12px; }
        .site-main input:not([type="checkbox"]):not([type="file"]),
        .site-main select, .site-main textarea {
            width: 100%;
            min-height: 44px;
            margin-top: 7px;
            padding: 10px 12px;
            border: 1px solid #dce1e9;
            border-radius: 9px;
            color: var(--ink);
            background: #fff;
        }
        .site-main label {
            display: inline-block;
            width: 100%;
            margin: 8px 0;
            color: #4c566b;
            font-size: 13px;
            font-weight: 620;
        }
        .site-main label span { display: block; }
        .form-actions { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
        .form-actions.justify-end { justify-content: flex-end; }
        .subject-card {
            margin-top: 18px;
            padding: 22px 22px 16px;
        }
        .subject-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .subject-card-header h3 { margin: 0 0 4px; font-size: 1.15rem; }
        .subject-card-header p { margin: 0; color: #697386; font-size: 12px; }
        .subject-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .subject-edit-form { margin-top: 14px; }
        .button-ghost {
            border-color: #dfe4ee;
            color: #485267;
            background: #fff;
        }
        .button-ghost:hover { border-color: #c9c4ff; color: var(--brand); background: #faf9ff; }
        .empty-state {
            padding: 26px 18px;
            border: 1px dashed #dfe6f1;
            border-radius: 14px;
            background: #fbfcff;
            text-align: center;
        }
        .empty-state h3 { margin: 0 0 8px; }
        .empty-state p { margin: 0; }
        .site-main textarea { min-height: 110px; resize: vertical; }
        .site-main label { display: inline-block; margin: 8px 12px 8px 0; color: #4c566b; font-size: 13px; font-weight: 620; }
        .site-main form { margin: 12px 0; }
        .site-main form button { margin: 6px 8px 6px 0; }
        .site-main [role="alert"] { padding: 14px 18px; border: 1px solid #ffd0d0; border-radius: 12px; color: #a33131; background: #fff5f5; }
        .site-main [role="status"] { padding: 13px 16px; border: 1px solid #bce8d3; border-radius: 12px; color: #176344; background: #effaf4; }
        .site-footer { border-top: 1px solid var(--line); background: var(--paper); }
        .footer-inner {
            display: flex;
            width: min(1160px, calc(100% - 48px));
            min-height: 76px;
            margin: 0 auto;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            color: #8790a1;
            font-size: 12px;
        }
        .footer-inner p { margin: 0; }
        .footer-links { display: flex; gap: 18px; }
        .footer-links a { color: #697386; }
        .footer-links a:hover { color: var(--brand); }

        @media (max-width: 760px) {
            .topbar-inner { width: min(100% - 32px, 560px); min-height: 68px; flex-wrap: wrap; gap: 8px; padding: 12px 0; }
            .nav-area { width: auto; flex-wrap: wrap; justify-content: flex-end; gap: 8px; }
            .nav-actions { flex-wrap: wrap; gap: 7px; }
            .nav-button { min-height: 35px; padding: 0 10px; font-size: 11px; }
            .account-dropdown { position: fixed; top: 70px; right: 16px; }
            .site-main { width: min(100% - 32px, 560px); padding: 30px 0 46px; }
            .workspace-shell { grid-template-columns: 1fr; gap: 18px; }
            .input-row { grid-template-columns: 1fr; gap: 4px; }
            .dashboard-header { align-items: flex-start; flex-direction: column; }
            .dashboard-stats { grid-template-columns: 1fr; }
            .dashboard-stats.admin-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dashboard-card-grid { grid-template-columns: 1fr; }
            .user-details-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .subject-table { min-width: 720px; }
            .workspace-sidebar { position: static; min-height: 0; }
            .sidebar-heading { display: none; }
            .sidebar-nav { display: flex; overflow-x: auto; gap: 6px; padding: 10px; scrollbar-width: thin; }
            .sidebar-label { display: none; }
            .sidebar-link { min-height: 37px; flex: 0 0 auto; padding: 0 10px; }
            .sidebar-active-dot, .sidebar-bottom { display: none; }
            .footer-inner { width: min(100% - 32px, 560px); min-height: 72px; flex-wrap: wrap; justify-content: center; padding: 14px 0; text-align: center; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
@php
    $webUnreadCount = auth('web')->check() ? auth('web')->user()->unreadNotifications()->count() : 0;
    $adminUnreadCount = auth('admin')->check() ? auth('admin')->user()->unreadNotifications()->count() : 0;
@endphp
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'StudySpace') }} home">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M4.5 5.5a2 2 0 0 1 2-2H19v16H6.5a2 2 0 0 1-2-2v-12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M4.5 16.5c0-1.1.9-2 2-2H19M9 7.5h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <span>{{ config('app.name', 'StudySpace') }}</span>
        </a>

        <div class="nav-area">
            <div class="nav-actions" aria-label="Account menu">
                @auth('web')
                    <a class="notification-link" href="{{ route('notifications.index') }}" aria-label="Notifications{{ $webUnreadCount ? ', '.$webUnreadCount.' unread' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @if ($webUnreadCount > 0)
                            <span class="notification-badge">{{ min($webUnreadCount, 99) }}</span>
                        @endif
                    </a>
                    <details class="account-menu">
                        <summary class="account-trigger">
                            <span class="account-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth('web')->user()->name, 0, 1)) }}</span>
                            <span class="account-trigger-copy">
                                <span>{{ auth('web')->user()->name }}</span>
                                <small>Student account</small>
                            </span>
                            <span class="account-chevron" aria-hidden="true"></span>
                        </summary>
                        <div class="account-dropdown">
                            <div class="account-dropdown-header">
                                <strong>{{ auth('web')->user()->name }}</strong>
                                <small>{{ auth('web')->user()->email }}</small>
                            </div>
                            <a href="{{ route('profile.show') }}">
                                <span aria-hidden="true">◉</span>
                                Profile settings
                            </a>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"><span aria-hidden="true">↪</span>Sign out</button>
                            </form>
                        </div>
                    </details>
                @endauth

                @auth('admin')
                    <a class="notification-link" href="{{ route('admin.notifications.index') }}" aria-label="Notifications{{ $adminUnreadCount ? ', '.$adminUnreadCount.' unread' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @if ($adminUnreadCount > 0)
                            <span class="notification-badge">{{ min($adminUnreadCount, 99) }}</span>
                        @endif
                    </a>
                    <details class="account-menu">
                        <summary class="account-trigger">
                            <span class="account-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth('admin')->user()->name, 0, 1)) }}</span>
                            <span class="account-trigger-copy">
                                <span>{{ auth('admin')->user()->name }}</span>
                                <small>Administrator</small>
                            </span>
                            <span class="account-chevron" aria-hidden="true"></span>
                        </summary>
                        <div class="account-dropdown">
                            <div class="account-dropdown-header">
                                <strong>{{ auth('admin')->user()->name }}</strong>
                                <small>{{ auth('admin')->user()->email }}</small>
                            </div>
                            <a href="{{ route('admin.profile.show') }}">
                                <span aria-hidden="true">◉</span>
                                Profile settings
                            </a>
                            <form method="post" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"><span aria-hidden="true">↪</span>Sign out</button>
                            </form>
                        </div>
                    </details>
                @endauth
            </div>
        </div>
    </div>
</header>

<div id="main-content" class="site-main" role="main">
    @if (request()->routeIs('admin.*') && auth('admin')->check())
        <div class="workspace-shell">
            <x-sidebar />
            <div class="workspace-content">{{ $slot }}</div>
        </div>
    @elseif (request()->routeIs(['dashboard', 'profile.show', 'notifications.*', 'subject.index', 'subjects.create', 'notes.*']) && auth('web')->check())
        <div class="workspace-shell">
            <x-sidebar />
            <div class="workspace-content">{{ $slot }}</div>
        </div>
    @else
        {{ $slot }}
    @endif
</div>

<footer class="site-footer">
    <div class="footer-inner">
        <p>© {{ now()->year }} {{ config('app.name', 'StudySpace') }}. Keep learning, one note at a time.</p>
        <nav class="footer-links" aria-label="Footer navigation">
            <!-- <a href="{{ url('/') }}">Home</a> -->
            @guest('web') <a href="{{ route('login') }}">User sign in</a> @endguest
            @guest('admin') <a href="{{ route('admin.login') }}">Admin sign in</a> @endguest
        </nav>
    </div>
</footer>
</body>
</html>
