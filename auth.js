/**
 * KILIMO SMART — Auth Module
 * localStorage-based demo authentication
 */

const AUTH_KEY = "ks_auth_user";
const USERS_KEY = "ks_auth_users";

const DEMO_ACCOUNTS = [
  {
    id: 1,
    name: "Yohana Machuma",
    email: "yohana@kilimosmart.tz",
    password: "admin123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 001",
    avatar: "👨‍💼",
    title: "Chief Executive Officer",
  },
  {
    id: 2,
    name: "William Msafiri",
    email: "william@kilimosmart.tz",
    password: "cto123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 002",
    avatar: "👨‍💻",
    title: "Chief Technology Officer",
  },
  {
    id: 3,
    name: "Enimelda Raphael",
    email: "enimelda@kilimosmart.tz",
    password: "enimelda123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 003",
    avatar: "👩‍🌾",
    title: "Digital Extension Officer",
  },
  {
    id: 4,
    name: "Karol Vicent",
    email: "karol@kilimosmart.tz",
    password: "karol123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 004",
    avatar: "🌿",
    title: "Agri-Tech Expert (Board)",
  },
  {
    id: 5,
    name: "Priscar Laurence Mrope",
    email: "priscar@kilimosmart.tz",
    password: "priscar123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 005",
    avatar: "📊",
    title: "Cashier & Accountant",
  },
  {
    id: 6,
    name: "Saidi Bahati Ally",
    email: "saidi@kilimosmart.tz",
    password: "saidi123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 006",
    avatar: "🚜",
    title: "Operations & Field Manager",
  },
  {
    id: 7,
    name: "Amani Amon",
    email: "amani@kilimosmart.tz",
    password: "amani123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 007",
    avatar: "👥",
    title: "HR & Training Coordinator",
  },
  {
    id: 8,
    name: "Maliki Moshi Luwungo",
    email: "maliki@kilimosmart.tz",
    password: "maliki123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 008",
    avatar: "⚖️",
    title: "Monitoring & Evaluation Officer",
  },
  {
    id: 9,
    name: "Maulida Nuru Hoseni",
    email: "maulida@kilimosmart.tz",
    password: "maulida123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 009",
    avatar: "💰",
    title: "Finance & Investment Manager",
  },
  {
    id: 10,
    name: "Mwaija Halfani Mnyachi",
    email: "mwaija@kilimosmart.tz",
    password: "mwaija123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 010",
    avatar: "🤝",
    title: "Community & Partnership Ambassador",
  },
  {
    id: 11,
    name: "Coletha Deodatus Lwala",
    email: "coletha@kilimosmart.tz",
    password: "coletha123",
    role: "admin",
    region: "Dar es Salaam",
    phone: "+255 754 100 011",
    avatar: "📣",
    title: "Marketing & Logistics Officer",
  },
  {
    id: 12,
    name: "Amina Juma",
    email: "amina@farmer.tz",
    password: "farmer123",
    role: "farmer",
    region: "Morogoro",
    phone: "+255 712 111 222",
    avatar: "👩‍🌾",
    title: "Mkulima",
  },
  {
    id: 13,
    name: "Demo Farmer",
    email: "demo@kilimosmart.tz",
    password: "demo",
    role: "farmer",
    region: "Arusha",
    phone: "+255 754 999 000",
    avatar: "🧑‍🌾",
    title: "Mkulima",
  },
];

function loadStoredUsers() {
  try {
    const raw = localStorage.getItem(USERS_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveStoredUsers(users) {
  localStorage.setItem(USERS_KEY, JSON.stringify(users));
}

function getAllAccounts() {
  return DEMO_ACCOUNTS.concat(loadStoredUsers());
}

function login(email, password) {
  const account = getAllAccounts().find(
    (a) =>
      a.email.toLowerCase() === email.toLowerCase() && a.password === password,
  );
  if (account) {
    const session = { ...account };
    delete session.password;
    session.loginTime = new Date().toISOString();
    localStorage.setItem(AUTH_KEY, JSON.stringify(session));
    return { ok: true, user: session };
  }
  return { ok: false, error: "Barua pepe au nywila si sahihi." };
}

function register(name, phone, email, region, password) {
  if (!name || !phone || !email || !region || !password) {
    return { ok: false, error: "Tafadhali jaza sehemu zote." };
  }
  if (password.length < 6) {
    return { ok: false, error: "Nywila iwe na herufi 6 au zaidi." };
  }
  const exists = getAllAccounts().find(
    (a) => a.email.toLowerCase() === email.toLowerCase(),
  );
  if (exists) {
    return { ok: false, error: "Barua pepe tayari ipo kwenye mfumo." };
  }
  const newUser = {
    id: Date.now(),
    name,
    email,
    phone,
    region,
    role: "farmer",
    avatar: "🧑‍🌾",
    password,
    loginTime: new Date().toISOString(),
  };
  const storedUsers = loadStoredUsers();
  storedUsers.push(newUser);
  saveStoredUsers(storedUsers);
  const session = { ...newUser };
  delete session.password;
  localStorage.setItem(AUTH_KEY, JSON.stringify(session));
  return { ok: true, user: session };
}

function logout() {
  localStorage.removeItem(AUTH_KEY);
  window.location.href = "index.html";
}

function getSession() {
  try {
    const raw = localStorage.getItem(AUTH_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function requireAuth() {
  const session = getSession();
  if (!session) {
    window.location.href = "index.html";
    return null;
  }
  return session;
}

function requireAdmin() {
  const session = requireAuth();
  if (session && session.role !== "admin") {
    window.location.href = "dashboard.html";
    return null;
  }
  return session;
}

// Populate user info in topbar/sidebar from session
function populateUserUI(session) {
  const nameEls = document.querySelectorAll("[data-user-name]");
  const roleEls = document.querySelectorAll("[data-user-role]");
  const avatarEls = document.querySelectorAll("[data-user-avatar]");
  const regionEls = document.querySelectorAll("[data-user-region]");

  nameEls.forEach((el) => (el.textContent = session.name || "Mkulima"));
  roleEls.forEach(
    (el) =>
      (el.textContent = session.title || (session.role === "admin" ? "Msimamizi" : "Mkulima")),
  );
  avatarEls.forEach((el) => (el.textContent = session.avatar || "🧑‍🌾"));
  regionEls.forEach((el) => (el.textContent = session.region || "—"));

  // Set up logout buttons
  document.querySelectorAll("[data-logout]").forEach((btn) => {
    btn.addEventListener("click", logout);
  });
}
