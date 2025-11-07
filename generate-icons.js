// اسکریپت تولید آیکون‌های PWA
// نصب: npm install sharp
// اجرا: node generate-icons.js

const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

// سایزهای مورد نیاز
const sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// ایجاد پوشه icons اگر وجود ندارد
const iconsDir = path.join(__dirname, 'public', 'icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

// خواندن SVG
const svgBuffer = fs.readFileSync(path.join(iconsDir, 'icon.svg'));

// تولید آیکون‌ها
async function generateIcons() {
    console.log('🎨 شروع تولید آیکون‌ها...\n');

    for (const size of sizes) {
        try {
            await sharp(svgBuffer)
                .resize(size, size)
                .png()
                .toFile(path.join(iconsDir, `icon-${size}x${size}.png`));
            
            console.log(`✅ آیکون ${size}x${size} ساخته شد`);
        } catch (error) {
            console.error(`❌ خطا در ساخت ${size}x${size}:`, error.message);
        }
    }

    // ساخت maskable icon با padding
    try {
        await sharp(svgBuffer)
            .resize(410, 410)
            .extend({
                top: 51,
                bottom: 51,
                left: 51,
                right: 51,
                background: { r: 79, g: 70, b: 229, alpha: 1 }
            })
            .png()
            .toFile(path.join(iconsDir, 'maskable-icon-512x512.png'));
        
        console.log('✅ Maskable icon 512x512 ساخته شد');
    } catch (error) {
        console.error('❌ خطا در ساخت maskable icon:', error.message);
    }

    // ساخت آیکون‌های shortcut
    try {
        // آیکون شروع آزمون (با علامت document)
        await sharp(Buffer.from(`
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96">
                <rect width="96" height="96" fill="#10b981" rx="20"/>
                <path d="M30 25h26l10 10v31a4 4 0 01-4 4H30a4 4 0 01-4-4V29a4 4 0 014-4z" fill="white"/>
                <path d="M56 25v10h10z" fill="#d1fae5"/>
                <line x1="38" y1="45" x2="58" y2="45" stroke="#10b981" stroke-width="2"/>
                <line x1="38" y1="52" x2="58" y2="52" stroke="#10b981" stroke-width="2"/>
                <line x1="38" y1="59" x2="50" y2="59" stroke="#10b981" stroke-width="2"/>
            </svg>
        `))
            .resize(96, 96)
            .png()
            .toFile(path.join(iconsDir, 'shortcut-exam.png'));
        
        console.log('✅ Shortcut icon (آزمون) ساخته شد');

        // آیکون پروفایل (با علامت user)
        await sharp(Buffer.from(`
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96">
                <rect width="96" height="96" fill="#6366f1" rx="20"/>
                <circle cx="48" cy="35" r="12" fill="white"/>
                <path d="M28 70c0-11 9-20 20-20s20 9 20 20z" fill="white"/>
            </svg>
        `))
            .resize(96, 96)
            .png()
            .toFile(path.join(iconsDir, 'shortcut-profile.png'));
        
        console.log('✅ Shortcut icon (پروفایل) ساخته شد');
    } catch (error) {
        console.error('❌ خطا در ساخت shortcut icons:', error.message);
    }

    console.log('\n✨ تمام آیکون‌ها با موفقیت ساخته شدند!');
}

generateIcons().catch(console.error);
