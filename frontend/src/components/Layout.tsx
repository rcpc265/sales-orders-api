
import { Outlet, Link } from 'react-router-dom';
import { LayoutDashboard, PlusCircle, Package } from 'lucide-react';

const Layout = () => {
  return (
    <div>
      <nav style={{ background: 'var(--surface-color)', borderBottom: '1px solid var(--surface-border)', padding: '1rem 0', position: 'sticky', top: 0, zIndex: 50, backdropFilter: 'blur(12px)' }}>
        <div className="container flex items-center justify-between">
          <Link to="/" className="flex items-center gap-2" style={{ color: 'white', fontWeight: 700, fontSize: '1.25rem', fontFamily: 'var(--font-display)' }}>
            <Package color="var(--primary-color)" />
            Nexus Orders
          </Link>
          <div className="flex items-center gap-6">
            <Link to="/" className="flex items-center gap-2" style={{ fontWeight: 500 }}>
              <LayoutDashboard size={18} /> Dashboard
            </Link>
            <Link to="/create" className="btn btn-primary" style={{ padding: '0.5rem 1rem' }}>
              <PlusCircle size={18} /> New Order
            </Link>
          </div>
        </div>
      </nav>
      <main className="container mt-8 animate-fade-in pb-8">
        <Outlet />
      </main>
    </div>
  );
};

export default Layout;
