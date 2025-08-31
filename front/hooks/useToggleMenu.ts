import { useState } from 'react';
import { UseToggleMenuReturn } from "@/types/index";

export function useToggleMenu(): UseToggleMenuReturn {
    const [open, setOpen] = useState(false);

    const toggleMenu = () => {
        setOpen((prev) => {
            const next = !prev;
            document.body.style.overflow = open ? 'auto' : 'hidden';
            return next;
        });
    };

    return { open, toggleMenu };
}