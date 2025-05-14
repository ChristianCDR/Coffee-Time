
import { useEffect, useState } from 'react';
import { useCoffeeProgress } from "@/hooks/useCoffeeProgress";
import { Queue, UseQueueStatusReturn } from "@/types/index";

export function useQueueStatus (): UseQueueStatusReturn {
    const { data, error } = useCoffeeProgress<Queue>();
    const [queue, setQueue] = useState<Queue>({'node': '', 'messages': 0});
    
    useEffect(() => {
        if (data && data.messages) {
            setQueue({node: data.node, messages: data.messages});
        }
    }, [data]);

    return { queue, error };
}