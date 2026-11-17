import os
import subprocess
import markdown
import re

def build_report():
    md_path = "DOKUMEN_PROYEK_AKHIR_BANTUIN.md"
    with open(md_path, "r", encoding="utf-8") as f:
        md_content = f.read()

    # Convert markdown to HTML
    html_body = markdown.markdown(
        md_content,
        extensions=['tables', 'fenced_code', 'toc', 'nl2br']
    )

    # Convert ```mermaid ... ``` code blocks to <pre class="mermaid">...</pre>
    html_body = re.sub(
        r'<pre><code class="language-mermaid">([\s\S]*?)</code></pre>',
        r'<div class="mermaid">\1</div>',
        html_body
    )

    full_html = f"""<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Proyek Akhir Mandiri RPL - BANTUIN - Yoga Fratama Fahdi Sardi (41257243)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({{
            startOnLoad: true,
            theme: 'default',
            themeVariables: {{
                primaryColor: '#EFF6FF',
                primaryTextColor: '#0F172A',
                primaryBorderColor: '#0284C7',
                lineColor: '#0369A1'
            }}
        }});
    </script>
    <style>
        @page {{
            size: A4;
            margin: 20mm 18mm 20mm 18mm;
            @bottom-center {{
                content: counter(page);
                font-family: 'Inter', sans-serif;
                font-size: 9pt;
                color: #64748B;
            }}
        }}

        * {{
            box-sizing: border-box;
        }}

        body {{
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 10.5pt;
            line-height: 1.65;
            color: #1E293B;
            background: #fff;
            margin: 0;
            padding: 0;
        }}

        /* Cover Page */
        .cover-page {{
            page-break-after: always;
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 40px;
        }}

        .cover-header {{
            text-align: center;
            border-bottom: 3px double #003049;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }}

        .cover-inst {{
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 1px;
            color: #003049;
            margin-bottom: 4px;
        }}

        .cover-sub-inst {{
            font-size: 10.5pt;
            font-weight: 600;
            color: #64748B;
        }}

        .cover-body {{
            text-align: center;
            margin: auto 0;
        }}

        .cover-doc-tag {{
            display: inline-block;
            background: #EFF6FF;
            color: #0078D4;
            border: 1px solid #BFDBFE;
            padding: 4px 16px;
            border-radius: 99px;
            font-size: 10pt;
            font-weight: 700;
            margin-bottom: 16px;
        }}

        .cover-title {{
            font-size: 24pt;
            font-weight: 800;
            color: #003049;
            line-height: 1.25;
            margin-bottom: 12px;
        }}

        .cover-app-name {{
            color: #0078D4;
        }}

        .cover-desc {{
            font-size: 12pt;
            color: #475569;
            max-width: 680px;
            margin: 0 auto 30px auto;
        }}

        .meta-card {{
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 20px 28px;
            text-align: left;
            max-width: 620px;
            margin: 0 auto;
            font-size: 10pt;
        }}

        .meta-card table {{
            width: 100%;
            border-collapse: collapse;
        }}

        .meta-card td {{
            padding: 5px 8px;
            vertical-align: top;
            border: none !important;
        }}

        .meta-card td:first-child {{
            font-weight: 700;
            color: #003049;
            width: 160px;
        }}

        .video-box {{
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            padding: 12px 18px;
            margin-top: 14px;
            font-size: 9.5pt;
        }}

        .video-box a {{
            color: #15803D;
            text-decoration: none;
            font-weight: 600;
        }}

        .video-box a:hover {{
            text-decoration: underline;
        }}

        /* Content Styling */
        h1 {{
            font-size: 16pt;
            font-weight: 800;
            color: #003049;
            border-bottom: 2px solid #0078D4;
            padding-bottom: 8px;
            margin-top: 36px;
            margin-bottom: 16px;
            page-break-after: avoid;
        }}

        h2 {{
            font-size: 13.5pt;
            font-weight: 700;
            color: #004870;
            margin-top: 24px;
            margin-bottom: 12px;
            page-break-after: avoid;
        }}

        h3 {{
            font-size: 11.5pt;
            font-weight: 700;
            color: #0F172A;
            margin-top: 20px;
            margin-bottom: 8px;
            page-break-after: avoid;
        }}

        p {{
            margin-bottom: 10px;
            text-align: justify;
        }}

        ul, ol {{
            margin-top: 4px;
            margin-bottom: 12px;
            padding-left: 24px;
        }}

        li {{
            margin-bottom: 4px;
        }}

        table {{
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }}

        th, td {{
            border: 1px solid #CBD5E1;
            padding: 8px 12px;
            text-align: left;
        }}

        th {{
            background: #F1F5F9;
            font-weight: 700;
            color: #003049;
        }}

        code {{
            font-family: 'JetBrains Mono', monospace;
            font-size: 9pt;
            background: #F1F5F9;
            color: #0F172A;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #E2E8F0;
        }}

        pre {{
            background: #0F172A;
            color: #F8FAFC;
            padding: 14px 18px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 8.5pt;
            line-height: 1.5;
            margin: 14px 0;
            page-break-inside: avoid;
        }}

        pre code {{
            background: transparent;
            color: inherit;
            padding: 0;
            border: none;
        }}

        .mermaid {{
            text-align: center;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 16px;
            margin: 16px 0;
            page-break-inside: avoid;
        }}

        hr {{
            border: none;
            border-top: 1px solid #E2E8F0;
            margin: 24px 0;
        }}

        .page-break {{
            page-break-before: always;
        }}
    </style>
</head>
<body>

    <!-- Cover Page -->
    <div class="cover-page">
        <div class="cover-header">
            <div class="cover-inst">PROGRAM STUDI S1 TEKNIK INFORMATIKA</div>
            <div class="cover-inst" style="color: #0078D4;">STMIK IKMI CIREBON</div>
            <div class="cover-sub-inst">Mata Kuliah: Rekayasa Perangkat Lunak (TIKB2503021) — Semester 3 (4 SKS)</div>
        </div>

        <div class="cover-body">
            <div class="cover-doc-tag">BERKAS LAPORAN TUGAS PROYEK AKHIR MANDIRI (OBE)</div>
            <div class="cover-title">
                <span class="cover-app-name">BANTUIN</span><br>
                Platform Layanan Jasa Perbaikan Rumah Tangga Berbasis Web & Kecerdasan Artifisial (AI)
            </div>
            <div class="cover-desc">
                Kompilasi Tunggal Dokumen PRD (IEEE 830), SDD (4 Pemodelan UML & UI/UX Figma), serta Laporan Pengujian Kode & CI/CD Pipeline DevOps.
            </div>

            <div class="meta-card">
                <table>
                    <tr>
                        <td>Nama Mahasiswa</td>
                        <td>: <strong>Yoga Fratama Fahdi Sardi</strong></td>
                    </tr>
                    <tr>
                        <td>NIM / Kelas</td>
                        <td>: <strong>41257243</strong> (Kelas 1C - 2025)</td>
                    </tr>
                    <tr>
                        <td>Dosen Pengampu</td>
                        <td>: <strong>Yudhistira Arie Wijaya, M.Kom., CDS.</strong></td>
                    </tr>
                    <tr>
                        <td>Repositori GitHub</td>
                        <td>: <a href="https://github.com/yogafrma/PROJECT-BANTUIN-PLATFORM-JASA-RUMAH-TANGGA" target="_blank">github.com/yogafrma/PROJECT-BANTUIN...</a></td>
                    </tr>
                </table>

                <div class="video-box">
                    <strong>Tautan Luaran Video Presentasi (YouTube):</strong><br>
                    • <strong>Video Progres 1 (PRD & Konsep):</strong> <a href="https://youtu.be/EDChMg5fP7E" target="_blank">https://youtu.be/EDChMg5fP7E</a><br>
                    • <strong>Video Progres 2 (UML & UI/UX):</strong> <a href="https://youtu.be/DvSUCWPcI6Q" target="_blank">https://youtu.be/DvSUCWPcI6Q</a><br>
                    • <strong>Video Final (Live Demo & CI/CD):</strong> <a href="https://youtu.be/vljBlnMVWDY" target="_blank">https://youtu.be/vljBlnMVWDY</a>
                </div>
            </div>
        </div>

        <div style="text-align: center; font-size: 9.5pt; color: #94A3B8; border-top: 1px solid #E2E8F0; padding-top: 14px;">
            STMIK IKMI Cirebon &bull; Tahun Akademik 2025/2026 &bull; Kurikulum Outcome-Based Education (OBE)
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        {html_body}
    </div>

</body>
</html>
"""

    html_out = "LAPORAN_PROYEK_AKHIR_RPL_BANTUIN.html"
    with open(html_out, "w", encoding="utf-8") as f:
        f.write(full_html)
    print(f"HTML report generated: {html_out}")

    # Generate PDF via Chrome / Edge Headless
    pdf_out = "LAPORAN_PROYEK_AKHIR_RPL_41257243_YOGA_FRATAMA.pdf"
    
    # Try Edge or Chrome
    browser_exe = None
    edge_path = r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
    chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
    
    if os.path.exists(edge_path):
        browser_exe = edge_path
    elif os.path.exists(chrome_path):
        browser_exe = chrome_path

    if browser_exe:
        abs_html = os.path.abspath(html_out)
        abs_pdf = os.path.abspath(pdf_out)
        cmd = [
            browser_exe,
            "--headless=new",
            "--disable-gpu",
            "--run-all-compositor-stages-before-draw",
            f"--print-to-pdf={abs_pdf}",
            "--print-to-pdf-no-header",
            abs_html
        ]
        print(f"Running headless browser print to PDF: {cmd}")
        res = subprocess.run(cmd, capture_output=True, text=True)
        if os.path.exists(pdf_out) and os.path.getsize(pdf_out) > 0:
            print(f"PDF successfully generated: {pdf_out} (Size: {os.path.getsize(pdf_out)} bytes)")
        else:
            print(f"Browser PDF print error: {res.stderr}")
    else:
        print("No headless browser found to print PDF.")

if __name__ == "__main__":
    build_report()
