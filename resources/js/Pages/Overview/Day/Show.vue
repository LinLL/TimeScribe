<script lang="ts" setup>
import ConfirmationDialog from '@/Components/dialogs/ConfirmationDialog.vue'
import Timeline from '@/Components/Timeline.vue'
import TimestampListItem from '@/Components/TimestampListItem.vue'
import TimestampListPlaceholderItem from '@/Components/TimestampListPlaceholderItem.vue'
import TimestampTypeBadge from '@/Components/TimestampTypeBadge.vue'
import { TimeWheel } from '@/Components/ui-custom/time-wheel'
import { Button } from '@/Components/ui/button'
import { Absence, Timestamp } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import moment from 'moment/min/moment-with-locales'

const props = defineProps<{
    date: string
    dayWorkTime: number
    dayPlan?: number
    timestamps: Timestamp[]
    absences: Absence[]
    dayBreakTime: number
    dayNoWorkTime: number
}>()

const calcDuration = (startTimestamp: string, endTimestamp?: string) =>
    Math.floor(moment(startTimestamp).diff(endTimestamp).valueOf() / 1000 / 60)

const startOfDay = moment(props.date, 'DD.MM.YYYY').format('YYYY-MM-DD 00:00:00')

if (window.Native) {
    window.Native.on('App\\Events\\TimerStarted', () => {
        router.flushAll()
        router.reload({
            only: ['timestamps'],
            showProgress: false
        })
    })
}
</script>

<template>
    <Head title="Day Overview" />
    <div class="mb-4 flex items-center gap-4">
        <div class="text-foreground/80 text-base font-medium">{{ $t('app.daily overview') }}</div>
        <div class="flex flex-1 items-center justify-center text-sm">
            <TimeWheel :date="props.date" route="overview.day.show" type="day" />
        </div>
        <div>
            <Button
                :as="Link"
                :href="route('overview.day.show', { date: moment().format('YYYY-MM-DD') })"
                class="z-20"
                prefetch
                size="sm"
                variant="outline"
            >
                {{ $t('app.today') }}
            </Button>
        </div>
    </div>
    <div class="flex grow flex-col overflow-hidden">
        <Timeline
            :overtime="Math.max(props.dayWorkTime - (props.dayPlan ?? 0) * 60 * 60, 0)"
            :timestamps="props.timestamps"
            :work-time="props.dayWorkTime"
            class="mb-6 shrink-0"
        />
        <div class="mb-6 flex gap-2">
            <TimestampTypeBadge type="vacation" v-if="props.absences.length && props.absences[0].type === 'vacation'" />
            <TimestampTypeBadge type="sick" v-if="props.absences.length && props.absences[0].type === 'sick'" />
            <TimestampTypeBadge :duration="props.dayWorkTime" type="work" v-if="!props.absences.length" />
            <TimestampTypeBadge :duration="props.dayBreakTime" type="break" />
            <TimestampTypeBadge :duration="props.dayNoWorkTime" type="noWork" />
            <TimestampTypeBadge
                :duration="Math.max(props.dayWorkTime - (props.dayPlan ?? 0) * 60 * 60, 0)"
                type="overtime"
            />
            <TimestampTypeBadge :duration="(props.dayPlan ?? 0) * 60 * 60" type="plan" />
        </div>
        <div class="grow space-y-1 overflow-y-auto pb-4" scroll-region>
            <TimestampListPlaceholderItem
                :start-of-day="startOfDay"
                v-if="props.timestamps.length === 0 || props.timestamps[0].started_at.date !== startOfDay"
            />
            <template :key="timestamp.id" v-for="(timestamp, index) in props.timestamps">
                <TimestampListPlaceholderItem
                    :duration="calcDuration(timestamp.started_at.date, props.timestamps[index - 1].ended_at?.date)"
                    :start-of-day="startOfDay"
                    :timestamp-after="timestamp"
                    :timestamp-before="props.timestamps[index - 1]"
                    v-if="
                        index > 0 && timestamp.started_at.formatted !== props.timestamps[index - 1].ended_at?.formatted
                    "
                />
                <TimestampListItem :timestamp="timestamp" />
            </template>
            <TimestampListPlaceholderItem
                :timestamp-before="props.timestamps[props.timestamps.length - 1]"
                v-if="
                    props.date !== moment().format('DD.MM.YYYY') &&
                    props.timestamps.length > 0 &&
                    moment(props.timestamps[props.timestamps.length - 1].ended_at?.date).format('HH:mm') !== '23:59'
                "
            />
        </div>
    </div>
    <ConfirmationDialog />
</template>
