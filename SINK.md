# Sink Statens vegvesen

Traffic counter data from Statens vegvesen for one county.

## Data

This is vehicle/bicycle counter data at various counter points for one
county.  The numbers are daily totals and contains bicycle or vehicle
counters.  In addition to total number of passed vehicles, it is
divided further down vehicle length.  Destination table is
`svv_traffic`.

## Source

Statens vegvesen has vast amount of data for all kinds of road related
information.  This data set is just a glimpse with relevance to public
transport.

## Usage

This set becomes handy in situations with major events or disturbances
in the traffic, where we want to compare public transport with the
overall traffic.

Also, pay attention to the `operational_status` column as a likely
filter parameter for validity of data.
