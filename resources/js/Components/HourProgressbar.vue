<script lang="ts" setup>
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/Components/ui/tooltip'
import { secToFormat } from '@/lib/utils'
import { BriefcaseBusiness, ChevronsLeftRightEllipsis, ClockArrowUp, Coffee } from 'lucide-vue-next'
import { computed } from 'vue'

const props = withDefaults(
    defineProps<{
        planTime?: number
        workTime?: number
        breakTime?: number
        noWorkTime?: number
    }>(),
    {
        planTime: 0,
        workTime: 0,
        breakTime: 0,
        noWorkTime: 0
    }
)

const percentages = computed(() => {
    let total = props.planTime + props.noWorkTime

    if (props.planTime < props.workTime) {
        total = props.workTime + props.noWorkTime
    }

    const calc_work_time_percent = props.workTime / total
    const calc_overtime_percent = 1 - props.planTime / props.workTime
    let work_percent = calc_work_time_percent
    let overtime_percent = calc_overtime_percent

    if (calc_overtime_percent > 0) {
        work_percent = calc_work_time_percent * (1 - calc_overtime_percent)
        overtime_percent = calc_work_time_percent * calc_overtime_percent
    }

    return {
        work: work_percent * 100,
        overtime: overtime_percent * 100,
        break: (props.breakTime / total) * 100,
        noWork: ((props.noWorkTime - props.breakTime) / total) * 100
    }
})
</script>

<template>
    <div class="bg-muted flex h-2 gap-[1px] overflow-clip rounded-full">
        <TooltipProvider :delay-duration="0">
            <Tooltip>
                <TooltipTrigger as-child>
                    <div :style="{ width: percentages.work + '%' }" class="bg-primary" v-if="percentages.work > 0" />
                </TooltipTrigger>
                <TooltipContent class="flex items-center gap-2">
                    <BriefcaseBusiness class="text-primary size-4" />
                    <div>
                        <div>
                            {{ $t('app.work hours') }}
                        </div>
                        <div class="font-bold">
                            {{
                                secToFormat(
                                    props.workTime > props.planTime ? props.planTime : props.workTime,
                                    false,
                                    true,
                                    true
                                )
                            }}
                            {{ $t('app.h') }}
                        </div>
                    </div>
                </TooltipContent>
            </Tooltip>
            <Tooltip>
                <TooltipTrigger as-child>
                    <div
                        :style="{ width: percentages.overtime + '%' }"
                        class="bg-yellow-400"
                        v-if="percentages.overtime > 0"
                    />
                </TooltipTrigger>
                <TooltipContent class="flex items-center gap-2">
                    <ClockArrowUp class="size-4 text-yellow-400" />
                    <div>
                        <div>
                            {{ $t('app.overtime') }}
                        </div>
                        <div class="font-bold">
                            {{ secToFormat(props.workTime - props.planTime, false, true, true) }}
                            {{ $t('app.h') }}
                        </div>
                    </div>
                </TooltipContent>
            </Tooltip>
            <Tooltip>
                <TooltipTrigger as-child>
                    <div
                        :style="{ width: percentages.break + '%' }"
                        class="ml-auto bg-pink-400"
                        v-if="percentages.break"
                    />
                </TooltipTrigger>
                <TooltipContent class="flex items-center gap-2">
                    <Coffee class="size-4 text-pink-400" />
                    <div>
                        <div>
                            {{ $t('app.break time') }}
                        </div>
                        <div class="font-bold">
                            {{ secToFormat(props.breakTime, false, true, true) }}
                            {{ $t('app.h') }}
                        </div>
                    </div>
                </TooltipContent>
            </Tooltip>
            <Tooltip>
                <TooltipTrigger as-child>
                    <div
                        :class="{
                            'ml-auto': !percentages.break
                        }"
                        :style="{ width: percentages.noWork + '%' }"
                        class="bg-rose-400"
                        v-if="percentages.noWork"
                    />
                </TooltipTrigger>
                <TooltipContent class="flex items-center gap-2">
                    <ChevronsLeftRightEllipsis class="size-4 text-rose-400" />
                    <div>
                        <div>
                            {{ $t('app.idle time') }}
                        </div>
                        <div class="font-bold">
                            {{ secToFormat(props.noWorkTime, false, true, true) }}
                            {{ $t('app.h') }}
                        </div>
                    </div>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    </div>
</template>
