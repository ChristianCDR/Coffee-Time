export default function Navbar() {
    return (
        <div className="flex justify-between items-center py-4 px-8 text-white "
            style={{ backgroundColor: "var(--dark-coffee)" }} 
        >
            <div className="flex flex-col items-center text-3xl font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M6 18a6.06 6.06 0 0 0 5.17-6a7.62 7.62 0 0 1 6.52-7.51l2.59-.37c-.07-.08-.13-.16-.21-.24c-3.26-3.26-9.52-2.28-14 2.18C2.28 9.9 1 15 2.76 18.46z"/><path fill="currentColor" d="M12.73 12a7.63 7.63 0 0 1-6.51 7.52l-2.46.35l.15.17c3.26 3.26 9.52 2.29 14-2.17C21.68 14.11 23 9 21.25 5.59l-3.34.48A6.05 6.05 0 0 0 12.73 12"/></svg>
                Coffee Time
            </div>
            <div className="flex justify-around basis-1/3">
                <a href="/order" className="hover:text-gray-300 p-2 border-b-1">Menu</a>
                <a href="#" className="hover:text-gray-300 p-2 border-b-1">About</a>  
                <a href="#" className="hover:text-gray-300 p-2 px-5 border-white border-1 rounded-xl">S'inscrire</a>
                <a href="#" className="hover:text-gray-300 p-2 px-5 border-white border-1 rounded-xl">Se connecter</a>
            </div>

        </div>
    );
}