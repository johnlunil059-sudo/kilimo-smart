/**
 * KILIMO SMART — Seed Data
 * Realistic Tanzanian agricultural data for demo/development
 */

// ── CROPS ─────────────────────────────────────────────────────
const CROPS = [
  { id: 'maize',       name: 'Mahindi (Maize)',     emoji: '🌽', color: '#e8b830', unit: 'kg' },
  { id: 'rice',        name: 'Mchele (Rice)',        emoji: '🌾', color: '#87d494', unit: 'kg' },
  { id: 'beans',       name: 'Maharagwe (Beans)',    emoji: '🫘', color: '#c97b3a', unit: 'kg' },
  { id: 'cassava',     name: 'Muhogo (Cassava)',     emoji: '🥔', color: '#d4a0c0', unit: 'kg' },
  { id: 'groundnuts',  name: 'Karanga (Groundnuts)', emoji: '🥜', color: '#e8946a', unit: 'kg' },
  { id: 'sunflower',   name: 'Alizeti (Sunflower)',  emoji: '🌻', color: '#ffd700', unit: 'kg' },
  { id: 'sorghum',     name: 'Mtama (Sorghum)',      emoji: '🌿', color: '#90ee90', unit: 'kg' },
  { id: 'sweet_potato',name: 'Viazi Vitamu',         emoji: '🍠', color: '#ff7f50', unit: 'kg' },
];

// ── REGIONS ───────────────────────────────────────────────────
const REGIONS = [
  { id: 'dar',       name: 'Dar es Salaam', zone: 'Coastal' },
  { id: 'morogoro',  name: 'Morogoro',      zone: 'Central' },
  { id: 'iringa',    name: 'Iringa',        zone: 'Southern Highlands' },
  { id: 'dodoma',    name: 'Dodoma',        zone: 'Central' },
  { id: 'arusha',    name: 'Arusha',        zone: 'Northern' },
  { id: 'mbeya',     name: 'Mbeya',         zone: 'Southern Highlands' },
  { id: 'mwanza',    name: 'Mwanza',        zone: 'Lake Zone' },
  { id: 'tanga',     name: 'Tanga',         zone: 'Coastal' },
];

// ── CURRENT MARKET PRICES (TZS per kg) ────────────────────────
// Format: { cropId: { regionId: { price, change, trend } } }
const MARKET_PRICES_RAW = [
  // MAIZE
  { crop:'maize',       region:'dar',      price:850,  change:+2.4,  trend:'up' },
  { crop:'maize',       region:'morogoro', price:680,  change:-1.2,  trend:'down' },
  { crop:'maize',       region:'iringa',   price:600,  change:+0.8,  trend:'up' },
  { crop:'maize',       region:'dodoma',   price:720,  change:0,     trend:'flat' },
  { crop:'maize',       region:'arusha',   price:790,  change:+3.1,  trend:'up' },
  { crop:'maize',       region:'mbeya',    price:640,  change:-0.5,  trend:'down' },
  { crop:'maize',       region:'mwanza',   price:740,  change:+1.8,  trend:'up' },
  { crop:'maize',       region:'tanga',    price:820,  change:+1.1,  trend:'up' },

  // RICE
  { crop:'rice',        region:'dar',      price:2200, change:+1.5,  trend:'up' },
  { crop:'rice',        region:'morogoro', price:1850, change:+0.3,  trend:'up' },
  { crop:'rice',        region:'iringa',   price:2100, change:-2.1,  trend:'down' },
  { crop:'rice',        region:'dodoma',   price:2050, change:0,     trend:'flat' },
  { crop:'rice',        region:'arusha',   price:2300, change:+2.8,  trend:'up' },
  { crop:'rice',        region:'mbeya',    price:1980, change:-0.8,  trend:'down' },
  { crop:'rice',        region:'mwanza',   price:2150, change:+1.2,  trend:'up' },
  { crop:'rice',        region:'tanga',    price:2080, change:+0.6,  trend:'up' },

  // BEANS
  { crop:'beans',       region:'dar',      price:3200, change:+4.2,  trend:'up' },
  { crop:'beans',       region:'morogoro', price:2800, change:+1.8,  trend:'up' },
  { crop:'beans',       region:'iringa',   price:2650, change:-1.5,  trend:'down' },
  { crop:'beans',       region:'dodoma',   price:2900, change:0,     trend:'flat' },
  { crop:'beans',       region:'arusha',   price:3100, change:+3.5,  trend:'up' },
  { crop:'beans',       region:'mbeya',    price:2750, change:-0.9,  trend:'down' },
  { crop:'beans',       region:'mwanza',   price:2950, change:+2.2,  trend:'up' },
  { crop:'beans',       region:'tanga',    price:3050, change:+1.3,  trend:'up' },

  // CASSAVA
  { crop:'cassava',     region:'dar',      price:450,  change:+1.0,  trend:'up' },
  { crop:'cassava',     region:'morogoro', price:320,  change:0,     trend:'flat' },
  { crop:'cassava',     region:'iringa',   price:300,  change:-2.0,  trend:'down' },
  { crop:'cassava',     region:'dodoma',   price:380,  change:+0.5,  trend:'up' },
  { crop:'cassava',     region:'arusha',   price:490,  change:+1.8,  trend:'up' },
  { crop:'cassava',     region:'mbeya',    price:350,  change:-1.1,  trend:'down' },
  { crop:'cassava',     region:'mwanza',   price:410,  change:+0.7,  trend:'up' },
  { crop:'cassava',     region:'tanga',    price:470,  change:+2.3,  trend:'up' },

  // GROUNDNUTS
  { crop:'groundnuts',  region:'dar',      price:4200, change:+5.1,  trend:'up' },
  { crop:'groundnuts',  region:'morogoro', price:3800, change:+2.4,  trend:'up' },
  { crop:'groundnuts',  region:'iringa',   price:3600, change:-1.8,  trend:'down' },
  { crop:'groundnuts',  region:'dodoma',   price:3950, change:0,     trend:'flat' },
  { crop:'groundnuts',  region:'arusha',   price:4100, change:+3.8,  trend:'up' },
  { crop:'groundnuts',  region:'mbeya',    price:3700, change:-0.7,  trend:'down' },
  { crop:'groundnuts',  region:'mwanza',   price:3850, change:+1.5,  trend:'up' },
  { crop:'groundnuts',  region:'tanga',    price:4050, change:+2.1,  trend:'up' },

  // SUNFLOWER
  { crop:'sunflower',   region:'dar',      price:1900, change:+2.8,  trend:'up' },
  { crop:'sunflower',   region:'morogoro', price:1650, change:+0.4,  trend:'up' },
  { crop:'sunflower',   region:'iringa',   price:1580, change:-1.0,  trend:'down' },
  { crop:'sunflower',   region:'dodoma',   price:1720, change:0,     trend:'flat' },
  { crop:'sunflower',   region:'arusha',   price:1850, change:+3.2,  trend:'up' },
  { crop:'sunflower',   region:'mbeya',    price:1600, change:-0.5,  trend:'down' },
  { crop:'sunflower',   region:'mwanza',   price:1780, change:+1.9,  trend:'up' },
  { crop:'sunflower',   region:'tanga',    price:1870, change:+1.4,  trend:'up' },

  // SORGHUM
  { crop:'sorghum',     region:'dar',      price:780,  change:+1.2,  trend:'up' },
  { crop:'sorghum',     region:'morogoro', price:620,  change:-0.8,  trend:'down' },
  { crop:'sorghum',     region:'iringa',   price:590,  change:0,     trend:'flat' },
  { crop:'sorghum',     region:'dodoma',   price:660,  change:+1.5,  trend:'up' },
  { crop:'sorghum',     region:'arusha',   price:740,  change:+2.1,  trend:'up' },
  { crop:'sorghum',     region:'mbeya',    price:610,  change:-1.3,  trend:'down' },
  { crop:'sorghum',     region:'mwanza',   price:680,  change:+0.9,  trend:'up' },
  { crop:'sorghum',     region:'tanga',    price:760,  change:+1.8,  trend:'up' },

  // SWEET POTATO
  { crop:'sweet_potato',region:'dar',      price:1100, change:+3.5,  trend:'up' },
  { crop:'sweet_potato',region:'morogoro', price:880,  change:+1.0,  trend:'up' },
  { crop:'sweet_potato',region:'iringa',   price:820,  change:-2.5,  trend:'down' },
  { crop:'sweet_potato',region:'dodoma',   price:950,  change:0,     trend:'flat' },
  { crop:'sweet_potato',region:'arusha',   price:1050, change:+2.8,  trend:'up' },
  { crop:'sweet_potato',region:'mbeya',    price:860,  change:-1.0,  trend:'down' },
  { crop:'sweet_potato',region:'mwanza',   price:990,  change:+1.6,  trend:'up' },
  { crop:'sweet_potato',region:'tanga',    price:1080, change:+2.2,  trend:'up' },
];

// ── HISTORICAL PRICE DATA (last 7 days) ───────────────────────
function generateHistoricalPrices(basePrice, days = 7) {
  const prices = [];
  let current = basePrice;
  for (let i = days; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    const change = (Math.random() - 0.45) * basePrice * 0.04;
    current = Math.max(current + change, basePrice * 0.7);
    prices.push({
      date: date.toLocaleDateString('en-TZ', { month:'short', day:'numeric' }),
      price: Math.round(current),
    });
  }
  return prices;
}

// ── WEATHER DATA ───────────────────────────────────────────────
const WEATHER_DATA = {
  dar: {
    region: 'Dar es Salaam',
    current: { temp: 29, feels: 33, humidity: 78, wind: 12, condition: 'Partly Cloudy', icon: '⛅' },
    forecast: [
      { day: 'Leo',    icon: '⛅', high: 29, low: 24, rain: 20 },
      { day: 'Jumanne',icon: '🌧️', high: 27, low: 23, rain: 75 },
      { day: 'Jumatano',icon:'🌩️', high: 25, low: 22, rain: 85 },
      { day: 'Alhamisi',icon:'🌦️', high: 26, low: 23, rain: 40 },
      { day: 'Ijumaa', icon: '☀️', high: 28, low: 24, rain: 10 },
      { day: 'Jumamosi',icon:'☀️', high: 30, low: 25, rain: 5  },
      { day: 'Jumapili',icon:'⛅', high: 29, low: 24, rain: 15 },
    ],
    alerts: [{ type: 'warning', message: 'Heavy rainfall expected Tue–Wed. Protect harvested crops.' }],
  },
  morogoro: {
    region: 'Morogoro',
    current: { temp: 26, feels: 28, humidity: 65, wind: 8, condition: 'Sunny', icon: '☀️' },
    forecast: [
      { day: 'Leo',    icon: '☀️', high: 26, low: 18, rain: 5  },
      { day: 'Jumanne',icon: '⛅', high: 24, low: 17, rain: 20 },
      { day: 'Jumatano',icon:'🌦️', high: 23, low: 16, rain: 45 },
      { day: 'Alhamisi',icon:'⛅', high: 25, low: 17, rain: 25 },
      { day: 'Ijumaa', icon: '☀️', high: 27, low: 19, rain: 8  },
      { day: 'Jumamosi',icon:'☀️', high: 28, low: 20, rain: 5  },
      { day: 'Jumapili',icon:'⛅', high: 26, low: 18, rain: 15 },
    ],
    alerts: [],
  },
  iringa: {
    region: 'Iringa',
    current: { temp: 22, feels: 20, humidity: 55, wind: 15, condition: 'Windy', icon: '💨' },
    forecast: [
      { day: 'Leo',    icon: '⛅', high: 22, low: 12, rain: 15 },
      { day: 'Jumanne',icon: '☀️', high: 24, low: 13, rain: 5  },
      { day: 'Jumatano',icon:'☀️', high: 25, low: 14, rain: 5  },
      { day: 'Alhamisi',icon:'🌦️', high: 21, low: 11, rain: 50 },
      { day: 'Ijumaa', icon: '🌧️', high: 19, low: 10, rain: 70 },
      { day: 'Jumamosi',icon:'🌦️', high: 20, low: 11, rain: 35 },
      { day: 'Jumapili',icon:'☀️', high: 23, low: 12, rain: 10 },
    ],
    alerts: [{ type: 'info', message: 'Cold nights expected Thu–Fri. Good conditions for drying beans.' }],
  },
  arusha: {
    region: 'Arusha',
    current: { temp: 24, feels: 23, humidity: 60, wind: 10, condition: 'Clear', icon: '☀️' },
    forecast: [
      { day: 'Leo',    icon: '☀️', high: 24, low: 15, rain: 5  },
      { day: 'Jumanne',icon: '☀️', high: 25, low: 16, rain: 5  },
      { day: 'Jumatano',icon:'⛅', high: 23, low: 14, rain: 20 },
      { day: 'Alhamisi',icon:'⛅', high: 22, low: 13, rain: 25 },
      { day: 'Ijumaa', icon: '🌦️', high: 21, low: 12, rain: 45 },
      { day: 'Jumamosi',icon:'⛅', high: 22, low: 13, rain: 20 },
      { day: 'Jumapili',icon:'☀️', high: 24, low: 15, rain: 8  },
    ],
    alerts: [],
  },
};

// ── STORAGE HUBS ───────────────────────────────────────────────
const STORAGE_HUBS = [
  {
    id: 'hub-001',
    name: 'Kilimo Hub Morogoro Central',
    region: 'Morogoro',
    location: 'Morogoro Town, Kwamatope Area',
    capacity: 500,    // tonnes
    occupied: 312,
    temperature: 23,
    humidity: 52,
    status: 'active',
    pricePerTonne: 15000, // TZS/tonne/month
    phone: '+255 754 001 001',
    crops: ['Maize', 'Beans', 'Sorghum'],
    manager: 'Juma Kassim',
  },
  {
    id: 'hub-002',
    name: 'Kilimo Hub Iringa Highlands',
    region: 'Iringa',
    location: 'Iringa Municipality, Gangilonga',
    capacity: 300,
    occupied: 198,
    temperature: 20,
    humidity: 48,
    status: 'active',
    pricePerTonne: 13500,
    phone: '+255 754 002 002',
    crops: ['Maize', 'Beans', 'Groundnuts', 'Sunflower'],
    manager: 'Anna Mwakipesile',
  },
  {
    id: 'hub-003',
    name: 'Kilimo Hub Dodoma East',
    region: 'Dodoma',
    location: 'Dodoma City, Nala Zone',
    capacity: 400,
    occupied: 400,
    temperature: 25,
    humidity: 45,
    status: 'full',
    pricePerTonne: 14000,
    phone: '+255 754 003 003',
    crops: ['Maize', 'Sorghum', 'Groundnuts'],
    manager: 'Fatuma Ngowi',
  },
  {
    id: 'hub-004',
    name: 'Kilimo Hub Mbeya South',
    region: 'Mbeya',
    location: 'Mbeya City, Uyole Agricultural Area',
    capacity: 600,
    occupied: 280,
    temperature: 21,
    humidity: 50,
    status: 'active',
    pricePerTonne: 12500,
    phone: '+255 754 004 004',
    crops: ['Maize', 'Beans', 'Sweet Potato', 'Rice'],
    manager: 'Peter Silayo',
  },
];

// ── LOAN PRODUCTS ──────────────────────────────────────────────
const LOAN_PRODUCTS = [
  {
    id: 'loan-input',
    name: 'Agricultural Input Loan',
    description: 'For seeds, fertilisers, and farm inputs',
    minAmount: 500000,
    maxAmount: 2000000,
    interestRate: 12,
    tenure: 12,
    repaymentType: 'Monthly',
    eligibility: 'Active Kilimo Smart farmer, 3+ months on platform',
    emoji: '🌱',
  },
  {
    id: 'loan-equipment',
    name: 'Farm Equipment Loan',
    description: 'For irrigation, tools, and machinery',
    minAmount: 1000000,
    maxAmount: 5000000,
    interestRate: 14,
    tenure: 24,
    repaymentType: 'Monthly',
    eligibility: 'Verified farmer, group guarantor required',
    emoji: '🚜',
  },
  {
    id: 'loan-storage',
    name: 'Post-Harvest Storage Loan',
    description: 'Cover storage costs to avoid distress selling',
    minAmount: 200000,
    maxAmount: 800000,
    interestRate: 10,
    tenure: 6,
    repaymentType: 'Bullet (end of season)',
    eligibility: 'Active storage hub booking required',
    emoji: '🏪',
  },
];

// ── SAMPLE FARMERS ─────────────────────────────────────────────
const SAMPLE_FARMERS = [
  { id: 1, name: 'Amina Juma',      phone: '+255 712 111 222', region: 'Morogoro', crops: ['Maize','Beans'],         status: 'active',   joined: '2025-03-15', subscription: 'Premium' },
  { id: 2, name: 'Hassan Ally',     phone: '+255 713 222 333', region: 'Iringa',   crops: ['Maize','Sunflower'],     status: 'active',   joined: '2025-04-02', subscription: 'Free' },
  { id: 3, name: 'Grace Mwangi',    phone: '+255 714 333 444', region: 'Arusha',   crops: ['Beans','Groundnuts'],    status: 'active',   joined: '2025-04-10', subscription: 'Premium' },
  { id: 4, name: 'Omari Seleman',   phone: '+255 715 444 555', region: 'Dodoma',   crops: ['Sorghum','Cassava'],     status: 'inactive', joined: '2025-02-20', subscription: 'Free' },
  { id: 5, name: 'Rehema Ndagala',  phone: '+255 716 555 666', region: 'Mbeya',    crops: ['Maize','Sweet Potato'],  status: 'active',   joined: '2025-05-01', subscription: 'Premium' },
  { id: 6, name: 'Salum Mbwana',    phone: '+255 717 666 777', region: 'Mwanza',   crops: ['Rice','Cassava'],        status: 'active',   joined: '2025-05-12', subscription: 'Free' },
  { id: 7, name: 'Joyce Kiango',    phone: '+255 718 777 888', region: 'Tanga',    crops: ['Cassava','Sweet Potato'],status: 'active',   joined: '2025-05-20', subscription: 'Premium' },
  { id: 8, name: 'Daudi Mwasumu',   phone: '+255 719 888 999', region: 'Dar es Salaam', crops: ['Beans'],           status: 'active',   joined: '2025-06-01', subscription: 'Free' },
];

// ── RECENT TRANSACTIONS ────────────────────────────────────────
const RECENT_TRANSACTIONS = [
  { id: 'TXN-001', farmer: 'Amina Juma',    type: 'Subscription', amount: 2000,  method: 'M-Pesa',     status: 'completed', date: '2025-06-20' },
  { id: 'TXN-002', farmer: 'Grace Mwangi',  type: 'Storage Fee',  amount: 45000, method: 'Tigo Pesa',  status: 'completed', date: '2025-06-19' },
  { id: 'TXN-003', farmer: 'Rehema Ndagala',type: 'Subscription', amount: 2000,  method: 'Airtel Money',status:'completed', date: '2025-06-18' },
  { id: 'TXN-004', farmer: 'Hassan Ally',   type: 'Loan Repayment',amount:85000, method: 'M-Pesa',     status: 'completed', date: '2025-06-18' },
  { id: 'TXN-005', farmer: 'Joyce Kiango',  type: 'Subscription', amount: 2000,  method: 'M-Pesa',     status: 'pending',   date: '2025-06-17' },
  { id: 'TXN-006', farmer: 'Salum Mbwana',  type: 'Storage Fee',  amount: 30000, method: 'Tigo Pesa',  status: 'failed',    date: '2025-06-17' },
];

// ── PLATFORM KPIs ──────────────────────────────────────────────
const PLATFORM_KPIS = {
  totalFarmers:       487,
  activeFarmers:      312,
  premiumSubscribers: 148,
  weeklyLogins:       856,
  priceLookups:       2340,
  storageBooked:      210,   // tonnes
  mobileMoneyVolume:  3850000, // TZS
  systemUptime:       99.7,
  farmerNPS:          42,
};

// ── ADVISORY CONTENT ───────────────────────────────────────────
const ADVISORY_TIPS = [
  {
    id: 1,
    crop: 'maize',
    title: 'Planting Time Optimisation',
    body: 'Plant maize at the onset of long rains (March–April). Ensure soil temperature is above 18°C. Space rows 75cm apart, seeds 25cm apart for optimal yield.',
    author: 'Enimelda Raphael',
    date: '2025-06-15',
    region: 'All Regions',
    category: 'Planting',
    emoji: '🌽',
  },
  {
    id: 2,
    crop: 'beans',
    title: 'Post-Harvest Storage Tips',
    body: 'Dry beans to below 13% moisture before storage. Use hermetic bags (PICS bags) to prevent weevil infestation. Store in cool, dry conditions (below 25°C).',
    author: 'Enimelda Raphael',
    date: '2025-06-12',
    region: 'Southern Highlands',
    category: 'Post-Harvest',
    emoji: '🫘',
  },
  {
    id: 3,
    crop: 'rice',
    title: 'Water Management in Paddy Rice',
    body: 'Maintain 5cm water depth during tillering stage. Drain fields 2 weeks before harvest to allow soil to firm up. This improves grain quality and makes harvest easier.',
    author: 'Enimelda Raphael',
    date: '2025-06-08',
    region: 'Mbeya, Morogoro',
    category: 'Irrigation',
    emoji: '🌾',
  },
  {
    id: 4,
    crop: 'groundnuts',
    title: 'Preventing Aflatoxin Contamination',
    body: 'Harvest groundnuts before rains if possible. Dry quickly on raised platforms in the sun. Never store wet groundnuts — aflatoxin contamination reduces market value significantly.',
    author: 'Enimelda Raphael',
    date: '2025-06-05',
    region: 'All Regions',
    category: 'Post-Harvest',
    emoji: '🥜',
  },
];

// ── NOTIFICATIONS ──────────────────────────────────────────────
const NOTIFICATIONS = [
  { id: 1, type: 'price',   title: 'Maize price up 2.4% in Dar', time: '2 hours ago',  read: false },
  { id: 2, type: 'weather', title: 'Heavy rain alert: Dar Tue–Wed', time: '4 hours ago', read: false },
  { id: 3, type: 'loan',    title: 'Your loan application approved', time: '1 day ago',  read: true },
  { id: 4, type: 'storage', title: 'Booking confirmed: Hub Morogoro', time: '2 days ago', read: true },
  { id: 5, type: 'system',  title: 'New advisory tip added for Beans', time: '3 days ago', read: true },
];

// ── HELPERS ────────────────────────────────────────────────────
function formatTZS(amount) {
  return 'TZS ' + amount.toLocaleString('en-TZ');
}

function getPriceRow(cropId, regionId) {
  return MARKET_PRICES_RAW.find(r => r.crop === cropId && r.region === regionId);
}

function getCropById(id) { return CROPS.find(c => c.id === id); }
function getRegionById(id) { return REGIONS.find(r => r.id === id); }

function getHubOccupancyPct(hub) {
  return Math.round((hub.occupied / hub.capacity) * 100);
}

function getTrendArrow(trend) {
  if (trend === 'up')   return '↑';
  if (trend === 'down') return '↓';
  return '→';
}
